import { useEffect, useMemo, useState } from "react";
import { getFiles } from "../services/dashboardService";

import PageHeader from "../components/common/PageHeader";
import StatusBadge from "../components/common/StatusBadge";
import LoadingSpinner from "../components/common/LoadingSpinner";
import EmptyState from "../components/common/EmptyState";

function UploadHistory() {
  const [files, setFiles] = useState([]);
  const [loading, setLoading] = useState(true);

  const [search, setSearch] = useState("");
  const [businessDate, setBusinessDate] = useState("");
  const [fileType, setFileType] = useState("");

  useEffect(() => {
    loadFiles();
  }, []);

  async function loadFiles() {
    try {
      setLoading(true);

      const response = await getFiles();

      setFiles(response.data.data || []);
    } catch (err) {
      console.error("File Monitor Error:", err);
    } finally {
      setLoading(false);
    }
  }

  const filteredFiles = useMemo(() => {
    return files.filter((file) => {
      const searchText = (
        `${file.file_name} ${file.source?.source_name || ""} ${file.source?.source_type || ""}`
      ).toLowerCase();

      const matchesSearch = searchText.includes(search.toLowerCase());

      const matchesDate =
        !businessDate ||
        file.business_date?.substring(0, 10) === businessDate;

      const matchesType =
        !fileType ||
        file.file_type === fileType;

      return (
        matchesSearch &&
        matchesDate &&
        matchesType
      );
    });
  }, [files, search, businessDate, fileType]);

  if (loading) {
    return <LoadingSpinner text="Loading Enterprise File Monitor..." />;
  }

  return (
    <div className="space-y-6">

      <PageHeader
        title="Enterprise File Monitor"
        description="Monitor uploaded files, processing lifecycle and reconciliation readiness."
      />

      {/* Filter Section */}

      <div className="bg-white rounded-xl shadow border border-slate-200 p-5">

        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">

          <input
            type="text"
            placeholder="Search File / Source..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none"
          />

          <input
            type="date"
            value={businessDate}
            onChange={(e) => setBusinessDate(e.target.value)}
            className="border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none"
          />

          <select
            value={fileType}
            onChange={(e) => setFileType(e.target.value)}
            className="border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none"
          >
            <option value="">All Types</option>
            <option value="DAILY">Daily</option>
            <option value="MONTHLY">Monthly</option>
          </select>

          <button
            onClick={loadFiles}
            className="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-6 py-3 font-semibold"
          >
            Refresh
          </button>

        </div>

        <div className="mt-4 text-sm text-slate-500">
          Showing <strong>{filteredFiles.length}</strong> of{" "}
          <strong>{files.length}</strong> registered files.
        </div>

      </div>

      {/* Table */}

      {filteredFiles.length === 0 ? (
        <EmptyState
          title="No Matching Files"
          message="No uploaded files match the selected filters."
        />
      ) : (
        <div className="bg-white rounded-xl shadow border border-slate-200 overflow-x-auto">

          <table className="min-w-full text-sm">

            <thead className="bg-slate-100 text-slate-700">

              <tr>

                <th className="p-4 text-left">Source</th>

                <th className="p-4 text-left">Category</th>

                <th className="p-4 text-left">File Name</th>

                <th className="p-4 text-left">Business Date</th>

                <th className="p-4 text-left">File Type</th>

                <th className="p-4 text-left">Records</th>

                <th className="p-4 text-left">Processing</th>

                <th className="p-4 text-left">Reconciliation</th>

                <th className="p-4 text-left">Locked</th>

              </tr>

            </thead>

            <tbody>

              {filteredFiles.map((file) => (

                <tr
                  key={file.id}
                  className="border-b hover:bg-slate-50 transition"
                >

                  <td className="p-4 font-semibold">
                    {file.source?.source_name}
                  </td>

                  <td className="p-4">
                    {file.source?.source_type}
                  </td>

                  <td className="p-4">
                    {file.file_name}
                  </td>

                  <td className="p-4">
                    {file.business_date?.substring(0, 10) || "-"}
                  </td>

                  <td className="p-4">
                    {file.file_type}
                  </td>

                  <td className="p-4">
                    {file.processed_records}/{file.total_records}
                  </td>

                  <td className="p-4">
                    <StatusBadge value={file.processing_status} />
                  </td>

                  <td className="p-4">
                    <StatusBadge value={file.reconciliation_status} />
                  </td>

                  <td className="p-4">
                    {file.is_locked ? (
                      <span className="text-red-600 font-semibold">
                        Locked
                      </span>
                    ) : (
                      <span className="text-green-600 font-semibold">
                        Open
                      </span>
                    )}
                  </td>

                </tr>

              ))}

            </tbody>

          </table>

        </div>
      )}

    </div>
  );
}

export default UploadHistory;