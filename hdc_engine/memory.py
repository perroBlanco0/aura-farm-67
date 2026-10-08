"""Item memory: associative clean-up store for base concept hypervectors."""

from __future__ import annotations

import numpy as np

from .hypervector import D_DIM, HyperVector


class ItemMemory:
    """Nearest-neighbor clean-up memory over named base hypervectors.

    Base vectors are drawn randomly, guaranteeing pseudo-orthogonality
    (pairwise Hamming distance ~0.5 +- 0.01). Retrieval is a single SIMD
    XOR + popcount pass over the stored matrix.
    """

    def __init__(self, dimensions: int = D_DIM, seed: int | None = None):
        self.dimensions = dimensions
        self._rng = np.random.default_rng(seed)
        self._by_name: dict[str, HyperVector] = {}
        self._names: list[str] = []
        self._matrix: np.ndarray | None = None  # lazily stacked (n, n_words)

    def __len__(self) -> int:
        return len(self._names)

    def __contains__(self, name: str) -> bool:
        return name in self._by_name

    def names(self) -> list[str]:
        return list(self._names)

    def get(self, name: str) -> HyperVector:
        try:
            return self._by_name[name]
        except KeyError:
            raise KeyError(f"unknown item: {name!r}") from None

    def get_or_create(self, name: str) -> HyperVector:
        hv = self._by_name.get(name)
        if hv is None:
            hv = HyperVector.random(self.dimensions, self._rng)
            self._by_name[name] = hv
            self._names.append(name)
            self._matrix = None
        return hv

    def _as_matrix(self) -> np.ndarray:
        if self._matrix is None:
            self._matrix = np.stack(
                [self._by_name[n].words for n in self._names]
            )
        return self._matrix

    def distances(self, hv: HyperVector) -> np.ndarray:
        """Normalized Hamming distance from `hv` to every stored item."""
        if not self._names:
            raise ValueError("item memory is empty")
        return np.bitwise_count(self._as_matrix() ^ hv.words).sum(axis=1) / self.dimensions

    def nearest(self, hv: HyperVector) -> tuple[str, float]:
        """Return (name, distance) of the closest stored hypervector."""
        dist = self.distances(hv)
        idx = int(np.argmin(dist))
        return self._names[idx], float(dist[idx])

    def cleanup(self, hv: HyperVector) -> str:
        """Snap a noisy hypervector to its nearest stored concept name."""
        return self.nearest(hv)[0]
