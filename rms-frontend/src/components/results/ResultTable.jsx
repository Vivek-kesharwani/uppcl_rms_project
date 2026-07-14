import StatusBadge from "../common/StatusBadge";

function ResultTable({ results = [] }) {
  return (
    <div className="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
      <table className="min-w-full text-sm">
        <thead className="bg-slate-100 text-slate-700">
          <tr>
            <th className="p-4 text-left">Batch Code</th>
            <th className="p-4 text-left">Result File</th>
            <th className="p-4 text-left">Matching Set</th>
            <th className="p-4 text-left">Business Date</th>
            <th className="p-4 text-left">Total</th>
            <th className="p-4 text-left">Matched</th>
            <th className="p-4 text-left">Exceptions</th>
            <th className="p-4 text-left">Status</th>
          </tr>
        </thead>

        <tbody>
          {results.map((item) => (
            <tr key={item.id} className="border-b hover:bg-slate-50">
              <td className="p-4 font-semibold">
                {item.batch?.batch_code || item.batch_id}
              </td>

              <td className="p-4 break-all">{item.file_name}</td>

              <td className="p-4">
                {item.matching_set?.set_name || item.matching_set_id}
              </td>

              <td className="p-4">
                {item.business_date?.substring(0, 10) || "-"}
              </td>

              <td className="p-4">{item.total_records}</td>

              <td className="p-4 text-green-700 font-semibold">
                {item.matched_records}
              </td>

              <td className="p-4 text-red-700 font-semibold">
                {item.exception_records}
              </td>

              <td className="p-4">
                <StatusBadge status={item.status} />
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default ResultTable;