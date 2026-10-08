"""Symbolic reasoner: holistic record encoding + relational/analogy queries."""

from __future__ import annotations

from .hypervector import D_DIM, HyperVector
from .memory import ItemMemory


class HDCReasoner:
    """Binds role/filler pairs into holistic record vectors and answers
    relational queries by unbinding + clean-up — no training, O(1) per fact.

        M_record = (role1 (x) filler1) (+) (role2 (x) filler2) (+) ...

    where (x) is XOR binding and (+) is majority bundling. A query unbinds
    the role's hypervector out of the record vector and cleans the noisy
    result back to the nearest stored concept.
    """

    def __init__(self, dimensions: int = D_DIM, seed: int | None = None):
        self.memory = ItemMemory(dimensions=dimensions, seed=seed)
        self._records: dict[str, HyperVector] = {}

    def store_record(self, name: str, fields: dict[str, str]) -> HyperVector:
        bound = [
            self.memory.get_or_create(role).bind(
                self.memory.get_or_create(filler)
            )
            for role, filler in fields.items()
        ]
        self._records[name] = HyperVector.bundle(bound)
        return self._records[name]

    def record(self, name: str) -> HyperVector:
        try:
            return self._records[name]
        except KeyError:
            raise KeyError(f"unknown record: {name!r}") from None

    def query_relation(self, country: str, role: str) -> str:
        """Which filler plays `role` in `country`'s record?"""
        probe = self.memory.get(role).bind(self.record(country))
        return self.memory.cleanup(probe)

    def solve_analogy(self, entity_a: str, item_a: str, entity_b: str) -> str:
        """`item_a` is to `entity_a` as X is to `entity_b`.

        Unbind item_a out of entity_a's record to recover the role it plays,
        then unbind that role out of entity_b's record to recover the answer.
        """
        probe = self.memory.get(item_a).bind(self.record(entity_a))
        role_name, _ = self.memory.nearest(probe)
        probe_b = self.memory.get(role_name).bind(self.record(entity_b))
        return self.memory.cleanup(probe_b)
