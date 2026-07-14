import { useState } from "react";

function SearchFilters({
  matchingSets = [],
  filters,
  setFilters,
  onSearch,
  onReset,
}) {
  const update = (key, value) => {
    setFilters({
      ...filters,
      [key]: value,
    });
  };

  return (
    <div className="bg-white rounded-xl shadow border border-slate-200 p-6">

      <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

        {/* Search */}

        <div>
          <label className="block text-sm font-semibold mb-2">
            Search
          </label>

          <input
            type="text"
            placeholder="Txn ID / Consumer / Account / UTR"
            value={filters.keyword}
            onChange={(e) => update("keyword", e.target.value)}
            className="w-full border rounded-lg px-4 py-3"
          />
        </div>

        {/* Search Type */}

        <div>
          <label className="block text-sm font-semibold mb-2">
            Search Type
          </label>

          <select
            value={filters.searchType}
            onChange={(e) => update("searchType", e.target.value)}
            className="w-full border rounded-lg px-4 py-3"
          >
            <option value="ALL">All</option>
            <option value="TRANSACTION_ID">Transaction ID</option>
            <option value="CONSUMER_NUMBER">Consumer Number</option>
            <option value="ACCOUNT_NUMBER">Account Number</option>
            <option value="UTR_NUMBER">UTR Number</option>
            <option value="SETTLEMENT_REFERENCE">
              Settlement Reference
            </option>
          </select>
        </div>

        {/* Business Date */}

        <div>
          <label className="block text-sm font-semibold mb-2">
            Business Date
          </label>

          <input
            type="date"
            value={filters.businessDate}
            onChange={(e) => update("businessDate", e.target.value)}
            className="w-full border rounded-lg px-4 py-3"
          />
        </div>

        {/* Matching Set */}

        <div>
          <label className="block text-sm font-semibold mb-2">
            Matching Set
          </label>

          <select
            value={filters.matchingSet}
            onChange={(e) => update("matchingSet", e.target.value)}
            className="w-full border rounded-lg px-4 py-3"
          >
            <option value="">All Matching Sets</option>

            {matchingSets.map((set) => (
              <option key={set.id} value={set.id}>
                {set.set_name}
              </option>
            ))}
          </select>
        </div>

        {/* Result Status */}

        <div>
          <label className="block text-sm font-semibold mb-2">
            Result Status
          </label>

          <select
            value={filters.resultStatus}
            onChange={(e) => update("resultStatus", e.target.value)}
            className="w-full border rounded-lg px-4 py-3"
          >
            <option value="">All</option>
            <option value="MATCHED">Matched</option>
            <option value="EXCEPTION">Exception</option>
            <option value="PENDING">Pending</option>
          </select>
        </div>

        {/* Amount */}

        <div>
          <label className="block text-sm font-semibold mb-2">
            Amount
          </label>

          <input
            type="number"
            placeholder="Enter Amount"
            value={filters.amount}
            onChange={(e) => update("amount", e.target.value)}
            className="w-full border rounded-lg px-4 py-3"
          />
        </div>
      </div>

      <div className="flex justify-end gap-4 mt-8">

        <button
          onClick={onReset}
          className="px-6 py-3 rounded-lg border border-slate-300 hover:bg-slate-100"
        >
          Reset
        </button>

        <button
          onClick={onSearch}
          className="px-8 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
        >
          Search
        </button>

      </div>
    </div>
  );
}

export default SearchFilters;