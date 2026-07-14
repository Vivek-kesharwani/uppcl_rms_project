function ResultFilters({
  search,
  setSearch,
  businessDate,
  setBusinessDate,
  fileType,
  setFileType,
  onRefresh,
  total,
  shown,
}) {
  return (
    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
      <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
        <input
          type="text"
          placeholder="Search batch/result file..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="rounded-lg border px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500"
        />

        <input
          type="date"
          value={businessDate}
          onChange={(e) => setBusinessDate(e.target.value)}
          className="rounded-lg border px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500"
        />

        <select
          value={fileType}
          onChange={(e) => setFileType(e.target.value)}
          className="rounded-lg border px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Types</option>
          <option value="DAILY">Daily</option>
          <option value="MONTHLY">Monthly</option>
        </select>

        <button
          onClick={onRefresh}
          className="rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700"
        >
          Refresh
        </button>
      </div>

      <p className="mt-4 text-sm text-slate-500">
        Showing <strong>{shown}</strong> of <strong>{total}</strong> result files.
      </p>
    </div>
  );
}

export default ResultFilters;