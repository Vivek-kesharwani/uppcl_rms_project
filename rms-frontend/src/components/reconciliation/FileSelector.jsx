import { useMemo, useState } from "react";

function FileSelector({ title, files = [], selectedFileId, onSelect }) {
  const [search, setSearch] = useState("");
  const [businessDate, setBusinessDate] = useState("");
  const [fileType, setFileType] = useState("");

  const filteredFiles = useMemo(() => {
    return files.filter((file) => {
      const text = `${file.file_name} ${file.source?.source_name || ""} ${file.source?.source_type || ""}`.toLowerCase();

      const matchesSearch = text.includes(search.toLowerCase());

      const matchesDate =
        !businessDate || file.business_date?.substring(0, 10) === businessDate;

      const matchesType = !fileType || file.file_type === fileType;

      return matchesSearch && matchesDate && matchesType;
    });
  }, [files, search, businessDate, fileType]);

  return (
    <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
      <h2 className="mb-4 text-lg font-semibold text-slate-800">{title}</h2>

      <div className="space-y-4">
        <input
          type="text"
          placeholder="Search file/source..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
        />

        <input
          type="date"
          value={businessDate}
          onChange={(e) => setBusinessDate(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
        />

        <select
          value={fileType}
          onChange={(e) => setFileType(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
        >
          <option value="">All Types</option>
          <option value="DAILY">Daily</option>
          <option value="MONTHLY">Monthly</option>
        </select>

        <select
          value={selectedFileId}
          onChange={(e) => onSelect(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
        >
          <option value="">Select File</option>

          {filteredFiles.map((file) => (
            <option key={file.id} value={file.id}>
              {file.file_name} | {file.business_date?.substring(0, 10)} |{" "}
              {file.total_records} records
            </option>
          ))}
        </select>

        <p className="text-sm text-slate-500">
          Showing {filteredFiles.length} of {files.length} files
        </p>
      </div>
    </div>
  );
}

export default FileSelector;