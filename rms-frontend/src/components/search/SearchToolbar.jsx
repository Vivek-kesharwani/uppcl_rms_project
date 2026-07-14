function SearchToolbar({
  onRefresh,
  onExport,
  onClear,
  totalResults = 0,
}) {
  return (
    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

      <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div>

          <h2 className="text-xl font-semibold text-slate-800">
            Enterprise Transaction Search
          </h2>

          <p className="mt-1 text-sm text-slate-500">
            Search and analyze reconciled utility payment transactions.
          </p>

          <p className="mt-2 text-sm font-medium text-blue-600">
            Search Results : {totalResults}
          </p>

        </div>

        <div className="flex flex-wrap gap-3">

          <button
            onClick={onRefresh}
            className="rounded-lg border border-slate-300 bg-white px-5 py-2 font-medium hover:bg-slate-50"
          >
            Refresh
          </button>

          <button
            onClick={onExport}
            className="rounded-lg bg-green-600 px-5 py-2 font-medium text-white hover:bg-green-700"
          >
            Export CSV
          </button>

          <button
            onClick={onClear}
            className="rounded-lg bg-red-600 px-5 py-2 font-medium text-white hover:bg-red-700"
          >
            Clear Filters
          </button>

        </div>

      </div>

    </div>
  );
}

export default SearchToolbar;