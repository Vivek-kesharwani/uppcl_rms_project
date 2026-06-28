function RecentExceptions({ exceptions }) {
  return (
    <div className="bg-white rounded-xl shadow p-6 border border-slate-200">
      <h2 className="text-lg font-semibold text-slate-800 mb-4">
        Recent Exceptions
      </h2>

      <div className="overflow-x-auto">
        <table className="w-full text-sm">
          <thead className="bg-slate-100 text-slate-600">
            <tr>
              <th className="text-left p-3">Txn ID</th>
              <th className="text-left p-3">Code</th>
              <th className="text-left p-3">Severity</th>
              <th className="text-left p-3">Status</th>
            </tr>
          </thead>
          <tbody>
            {exceptions?.map((item) => (
              <tr key={item.id} className="border-b">
                <td className="p-3">{item.txn_id}</td>
                <td className="p-3">{item.exception_code}</td>
                <td className="p-3">{item.severity}</td>
                <td className="p-3 font-semibold">{item.status}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default RecentExceptions;