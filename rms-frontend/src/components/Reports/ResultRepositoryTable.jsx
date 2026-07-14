import StatusBadge from "../common/StatusBadge";

function ResultRepositoryTable({ resultFiles = [], onDownload }) {
  return (
    <div className="rounded-xl border border-slate-200 bg-white shadow-sm">
      <div className="border-b px-6 py-5">
        <h2 className="text-lg font-semibold text-slate-800">
          Previous Result Files
        </h2>
        <p className="mt-1 text-sm text-slate-500">
          Searchable repository of generated reconciliation result files.
        </p>
      </div>

      <div className="overflow-x-auto">
        <table className="min-w-full text-sm">
          <thead className="bg-slate-100 text-slate-700">
            <tr>
              <th className="p-4 text-left">Result File</th>
              <th className="p-4 text-left">Batch Code</th>
              <th className="p-4 text-left">Matching Set</th>
              <th className="p-4 text-left">Business Date</th>
              <th className="p-4 text-left">Records</th>
              <th className="p-4 text-left">Matched</th>
              <th className="p-4 text-left">Exceptions</th>
              <th className="p-4 text-left">Status</th>
              <th className="p-4 text-left">Action</th>
            </tr>
          </thead>

          <tbody>
            {resultFiles.length === 0 ? (
              <tr>
                <td colSpan="9" className="p-6 text-center text-slate-500">
                  No result files found.
                </td>
              </tr>
            ) : (
              resultFiles.map((item) => (
                <tr key={item.id} className="border-b hover:bg-slate-50">
                  <td className="p-4 font-semibold break-all">
                    {item.file_name}
                  </td>

                  <td className="p-4">
                    {item.batch?.batch_code || item.batch_id || "-"}
                  </td>

                  <td className="p-4">
                    {item.matching_set?.set_name ||
                      item.matchingSet?.set_name ||
                      item.matching_set_id ||
                      "-"}
                  </td>

                  <td className="p-4">
                    {item.business_date?.substring(0, 10) || "-"}
                  </td>

                  <td className="p-4">{item.total_records ?? 0}</td>

                  <td className="p-4 font-semibold text-green-700">
                    {item.matched_records ?? 0}
                  </td>

                  <td className="p-4 font-semibold text-red-700">
                    {item.exception_records ?? 0}
                  </td>

                  <td className="p-4">
                    <StatusBadge status={item.status || "READY"} />
                  </td>

                  <td className="p-4">
                    <button
                      onClick={() => onDownload(item)}
                      className="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                    >
                      Download
                    </button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default ResultRepositoryTable;