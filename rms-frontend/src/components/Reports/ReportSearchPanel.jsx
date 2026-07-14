function ReportSearchPanel({
  search,
  setSearch,
  matchingSetId,
  setMatchingSetId,
  businessDate,
  setBusinessDate,
  period,
  setPeriod,
  matchingSets = [],
  relatedResults = [],
  selectedResultId,
  setSelectedResultId,
  onSearch,
  onReset,
}) {
  return (
    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
      <div className="mb-4">
        <h2 className="text-lg font-semibold text-slate-800">Report Finder</h2>
        <p className="text-sm text-slate-500">
          Search previous reconciliation reports and select a related result file.
        </p>
      </div>

      <div className="grid grid-cols-1 gap-4 md:grid-cols-5">
        <input
          type="text"
          placeholder="Search batch/result file..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="rounded-lg border px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500"
        />

        <select
          value={matchingSetId}
          onChange={(e) => setMatchingSetId(e.target.value)}
          className="rounded-lg border px-4 py-3"
        >
          <option value="">All Matching Sets</option>
          {matchingSets.map((set) => (
            <option key={set.id} value={set.id}>
              {set.set_name}
            </option>
          ))}
        </select>

        <input
          type="date"
          value={businessDate}
          onChange={(e) => setBusinessDate(e.target.value)}
          className="rounded-lg border px-4 py-3"
        />

        <select
          value={period}
          onChange={(e) => setPeriod(e.target.value)}
          className="rounded-lg border px-4 py-3"
        >
          <option value="">All Periods</option>
          <option value="DAILY">Daily</option>
          <option value="MONTHLY">Monthly</option>
        </select>

        <div className="flex gap-3">
          <button
            onClick={onSearch}
            className="flex-1 rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700"
          >
            Search
          </button>

          <button
            onClick={onReset}
            className="flex-1 rounded-lg border border-slate-300 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-50"
          >
            Reset
          </button>
        </div>
      </div>

      <div className="mt-5">
        <label className="mb-2 block text-sm font-semibold text-slate-700">
          Related Result Files
        </label>

        <select
          value={selectedResultId}
          onChange={(e) => setSelectedResultId(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
        >
          <option value="">Select related result file</option>
          {relatedResults.map((file) => (
            <option key={file.id} value={file.id}>
              {file.file_name} | {file.business_date?.substring(0, 10)} |{" "}
              {file.batch?.batch_code}
            </option>
          ))}
        </select>

        <p className="mt-2 text-sm text-slate-500">
          Showing {relatedResults.length} related result files.
        </p>
      </div>
    </div>
  );
}

export default ReportSearchPanel;