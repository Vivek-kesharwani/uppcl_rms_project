import { useEffect, useState } from "react";
import api from "../services/api";
import PageHeader from "../components/common/PageHeader";
import StatusBadge from "../components/common/StatusBadge";
import LoadingSpinner from "../components/common/LoadingSpinner";
import EmptyState from "../components/common/EmptyState";

function UploadHistory() {
  const [uploads, setUploads] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadUploads();
  }, []);

  const loadUploads = async () => {
    try {
      const response = await api.get("/uploads");
      setUploads(response.data.data);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <LoadingSpinner text="Loading upload history..." />;
  }

  if (uploads.length === 0) {
    return (
      <EmptyState
        title="No uploads found"
        message="Upload your first CSV file to see history here."
      />
    );
  }

  return (
    <div>
      <PageHeader
        title="Upload History"
        subtitle="View all uploaded CSV files and their processing status"
      />

      <div className="bg-white rounded-xl shadow border border-slate-200 overflow-hidden">

        <table className="w-full">

          <thead className="bg-slate-100">

            <tr>

              <th className="p-4 text-left">Source</th>
              <th className="p-4 text-left">File</th>
              <th className="p-4 text-left">Rows</th>
              <th className="p-4 text-left">Status</th>
              <th className="p-4 text-left">Uploaded At</th>

            </tr>

          </thead>

          <tbody>

            {uploads.map((item) => (

              <tr
                key={item.id}
                className="border-b hover:bg-slate-50"
              >

                <td className="p-4">{item.source_type}</td>

                <td className="p-4 font-medium">
                  {item.file_name}
                </td>

                <td className="p-4">
                  {item.processed_rows}
                </td>

                <td className="p-4">
                  <StatusBadge value={item.status} />
                </td>

                <td className="p-4">
                  {new Date(item.created_at).toLocaleString()}
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