import { useState } from "react";
import { FaUpload } from "react-icons/fa";
import api from "../services/api";
import PageHeader from "../components/common/PageHeader";
import LoadingSpinner from "../components/common/LoadingSpinner";

function Upload() {
  const [files, setFiles] = useState({
    agency: null,
    billing: null,
    bank: null,
  });

  const [loadingType, setLoadingType] = useState("");
  const [message, setMessage] = useState("");
  const [reconciliation, setReconciliation] = useState(null);

  const uploadTypes = [
    { key: "agency", title: "Agency CSV", description: "Upload agency transaction records" },
    { key: "billing", title: "Billing CSV", description: "Upload billing transaction records" },
    { key: "bank", title: "Bank CSV", description: "Upload bank settlement records" },
  ];

  const handleFileChange = (type, file) => {
    setFiles((prev) => ({ ...prev, [type]: file }));
  };

  const uploadFile = async (type) => {
    if (!files[type]) {
      setMessage(`Please select ${type} CSV file`);
      return;
    }

    const formData = new FormData();
    formData.append("file", files[type]);

    setLoadingType(type);
    setMessage("");
    setReconciliation(null);

    try {
      const response = await api.post(`/${type}/upload`, formData);
      setMessage(`${type.toUpperCase()} upload successful. Rows: ${response.data.inserted_rows}`);
      setReconciliation(response.data.reconciliation || null);
    } catch (error) {
      setMessage(error.response?.data?.message || `${type.toUpperCase()} upload failed`);
    } finally {
      setLoadingType("");
    }
  };

  return (
    <div>
      <PageHeader
        title="Upload Files"
        subtitle="Upload Agency, Billing, and Bank CSV files for reconciliation"
      />

      {message && (
        <div className="mb-6 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
          {message}
        </div>
      )}

      {reconciliation && (
        <div className="mb-6 bg-white rounded-xl shadow border border-slate-200 p-5">
          <h2 className="font-semibold text-slate-800 mb-3">Reconciliation Summary</h2>
          <div className="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
            <Summary label="Total" value={reconciliation.total} />
            <Summary label="Matched" value={reconciliation.matched} />
            <Summary label="Exceptions" value={reconciliation.exceptions} />
            <Summary label="Amount Mismatch" value={reconciliation.amount_mismatch} />
            <Summary label="Missing Settlement" value={reconciliation.missing_settlement} />
          </div>
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {uploadTypes.map((item) => (
          <div key={item.key} className="bg-white rounded-xl shadow border border-slate-200 p-6">
            <div className="flex items-center gap-3 mb-3">
              <div className="bg-blue-100 text-blue-700 p-3 rounded-lg">
                <FaUpload />
              </div>
              <div>
                <h2 className="text-lg font-semibold text-slate-800">{item.title}</h2>
                <p className="text-sm text-slate-500">{item.description}</p>
              </div>
            </div>

            <input
              type="file"
              accept=".csv"
              onChange={(e) => handleFileChange(item.key, e.target.files[0])}
              className="w-full mt-4 mb-4 text-sm"
            />

            <button
              onClick={() => uploadFile(item.key)}
              disabled={loadingType === item.key}
              className="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 disabled:bg-slate-400"
            >
              {loadingType === item.key ? "Uploading..." : `Upload ${item.title}`}
            </button>
          </div>
        ))}
      </div>

      {loadingType && (
        <div className="mt-6">
          <LoadingSpinner text={`Uploading ${loadingType} file...`} />
        </div>
      )}
    </div>
  );
}

function Summary({ label, value }) {
  return (
    <div className="bg-slate-50 rounded-lg p-3">
      <p className="text-slate-500">{label}</p>
      <p className="text-xl font-bold text-slate-800">{value ?? 0}</p>
    </div>
  );
}

export default Upload;