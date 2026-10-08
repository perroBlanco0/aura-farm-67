"""Binary hypervector primitives for HD computing / Vector Symbolic Architecture.

A hypervector lives in {0,1}^D packed into uint64 words (157 words for
D = 10,000, ~1.25 KB per vector). All algebra is pure bitwise NumPy:
no floating point, no weights, no training.
"""

from __future__ import annotations

import numpy as np

D_DIM = 10_000
_WORD_BITS = 64


def _n_words(d: int) -> int:
    return (d + _WORD_BITS - 1) // _WORD_BITS


def _tail_mask(d: int) -> np.uint64:
    tail = d % _WORD_BITS
    return np.uint64((1 << tail) - 1) if tail else np.uint64(0xFFFF_FFFF_FFFF_FFFF)


def _unpack(words: np.ndarray, d: int) -> np.ndarray:
    # bitorder="little" keeps bit i of the vector == bit i of its uint64 word
    return np.unpackbits(words.view(np.uint8), bitorder="little")[:d]


def _pack(bits: np.ndarray, d: int) -> np.ndarray:
    padded = np.zeros(_n_words(d) * _WORD_BITS, dtype=np.uint8)
    padded[:d] = bits
    return np.packbits(padded, bitorder="little").view(np.uint64)


class HyperVector:
    """A D-bit binary hypervector backed by a uint64 word array."""

    __slots__ = ("d", "words")

    def __init__(self, words: np.ndarray, d: int = D_DIM):
        words = np.ascontiguousarray(words, dtype=np.uint64).copy()
        expected = _n_words(d)
        if words.shape != (expected,):
            raise ValueError(
                f"expected {expected} uint64 words for D={d}, got shape {words.shape}"
            )
        words[-1] &= _tail_mask(d)  # unused tail bits stay zero forever
        self.d = d
        self.words = words

    @classmethod
    def random(
        cls, d: int = D_DIM, rng: np.random.Generator | None = None
    ) -> "HyperVector":
        rng = rng if rng is not None else np.random.default_rng()
        words = np.frombuffer(rng.bytes(_n_words(d) * 8), dtype=np.uint64)
        return cls(words, d)

    @classmethod
    def zeros(cls, d: int = D_DIM) -> "HyperVector":
        return cls(np.zeros(_n_words(d), dtype=np.uint64), d)

    @classmethod
    def bundle(cls, vectors, d: int = D_DIM) -> "HyperVector":
        """Majority-rule superposition of multiple hypervectors.

        Bit i of the result is the majority vote of bit i across the inputs.
        Ties (even-sized bundles) break deterministically via a seeded RNG so
        repeated cleanups of the same bundle are stable.
        """
        vecs = [v if isinstance(v, HyperVector) else cls(v, d) for v in vectors]
        if not vecs:
            raise ValueError("cannot bundle an empty set")
        bits = np.stack([_unpack(v.words, v.d) for v in vecs])
        counts = bits.sum(axis=0)
        n = len(vecs)
        majority = counts * 2 > n
        ties = counts * 2 == n
        if np.any(ties):
            rng = np.random.default_rng(0x9E3779B97F4A7C15 ^ n ^ vecs[0].d)
            majority[ties] = rng.integers(0, 2, size=int(ties.sum())).astype(bool)
        return cls(_pack(majority.astype(np.uint8), vecs[0].d), vecs[0].d)

    def bind(self, other: "HyperVector") -> "HyperVector":
        """XOR binding: invertible via A ^ (A ^ B) == B."""
        self._check_dim(other)
        return HyperVector(self.words ^ other.words, self.d)

    def permute(self, shifts: int = 1) -> "HyperVector":
        """Circular bit rotation encoding sequence/role without losing orthogonality."""
        bits = np.roll(_unpack(self.words, self.d), shifts)
        return HyperVector(_pack(bits, self.d), self.d)

    def hamming_distance(self, other: "HyperVector") -> float:
        """Normalized Hamming distance: POPCNT(A ^ B) / D."""
        self._check_dim(other)
        return float(np.bitwise_count(self.words ^ other.words).sum()) / self.d

    def similarity(self, other: "HyperVector") -> float:
        return 1.0 - self.hamming_distance(other)

    def to_bits(self) -> np.ndarray:
        return _unpack(self.words, self.d).copy()

    def _check_dim(self, other: "HyperVector") -> None:
        if self.d != other.d:
            raise ValueError(f"dimension mismatch: {self.d} vs {other.d}")

    def __eq__(self, other) -> bool:
        return (
            isinstance(other, HyperVector)
            and self.d == other.d
            and bool((self.words == other.words).all())
        )

    def __hash__(self) -> int:
        return hash((self.d, self.words.tobytes()))

    def __repr__(self) -> str:
        return f"HyperVector(d={self.d}, density={self.density():.3f})"

    def density(self) -> float:
        return float(np.bitwise_count(self.words).sum()) / self.d
