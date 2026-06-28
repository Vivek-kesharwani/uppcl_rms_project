function RecentUploads({ uploads }) {
  return (
    <div className="bg-white rounded-xl shadow p-6 border border-slate-200">
      <h2 className="text-lg font-semibold text-slate-800 mb-4">
        Recent Uploads
      </h2>

      <div className="overflow-x-auto">
        <table className="w-full text-sm">
          <thead className="bg-slate-100 text-slate-600">
            <tr>
              <th className="text-left p-3">Source</th>
              <th className="text-left p-3">File</th>
              <th className="text-left p-3">Rows</th>
              <th className="text-left p-3">Status</th>
            </tr>
          </thead>
          <tbody>
            {uploads?.map((upload) => (
              <tr key={upload.id} className="border-b">
                <td className="p-3">{upload.source_type}</td>
                <td className="p-3">{upload.file_name}</td>
                <td className="p-3">{upload.processed_rows}</td>
                <td className="p-3 font-semibold">{upload.status}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default RecentUploads;