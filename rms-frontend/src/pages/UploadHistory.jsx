import { useEffect, useState } from "react";
import { getFiles } from "../services/api";
import PageHeader from "../components/common/PageHeader";
import StatusBadge from "../components/common/StatusBadge";
import LoadingSpinner from "../components/common/LoadingSpinner";
import EmptyState from "../components/common/EmptyState";

function UploadHistory() {
  const [files, setFiles] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadFiles();
  }, []);

  async function loadFiles() {
    try {
      const response = await getFiles();
      setFiles(response.data.data || []);
    } catch (error) {
      console.error("Files API error:", error);
    } finally {
      setLoading(false);
    }
  }

  if (loading) {
    return <LoadingSpinner text="Loading file monitor..." />;
  }

  if (files.length === 0) {
    return (
      <EmptyState
        title="No files found"
        message="No reconciliation source files have been registered yet."
      />
    );
  }

  return (
    <div>
      <PageHeader
        title="File Monitor"
        subtitle="Live source files registered, validated, and staged by the reconciliation engine"
      />

      <div className="bg-white rounded-xl shadow border border-slate-200 overflow-x-auto">
        <table className="w-full text-sm">
          <thead className="bg-slate-100 text-slate-600">
            <tr>
              <th className="p-4 text-left">Source ID</th>
              <th className="p-4 text-left">File Name</th>
              <th className="p-4 text-left">Type</th>
              <th className="p-4 text-left">Business Date</th>
              <th className="p-4 text-left">File Size</th>
              <th className="p-4 text-left">Records</th>
              <th className="p-4 text-left">Failed</th>
              <th className="p-4 text-left">Status</th>
            </tr>
          </thead>

          <tbody>
            {files.map((item) => (
              <tr key={item.id} className="border-b hover:bg-slate-50">
                <td className="p-4">{item.source_id}</td>

                <td className="p-4 font-medium">
                  {item.file_name}
                </td>

                <td className="p-4">{item.file_type}</td>

                <td className="p-4">
                  {item.business_date?.slice(0, 10)}
                </td>

                <td className="p-4">
                  {item.file_size} bytes
                </td>

                <td className="p-4">
                  {item.processed_records}/{item.total_records}
                </td>

                <td className="p-4">
                  {item.failed_records}
                </td>

                <td className="p-4">
                  <StatusBadge value={item.status} />
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

export default UploadHistory;