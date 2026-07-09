import { useEffect, useState } from "react";
import {
  getMatchingSets,
  getFilesForMatchingSet,
  runSelectedReconciliation,
} from "../services/reconciliationService";

function ReconciliationWorkbench() {
  const [matchingSets, setMatchingSets] = useState([]);
  const [selectedSet, setSelectedSet] = useState("");
  const [leftFiles, setLeftFiles] = useState([]);
  const [rightFiles, setRightFiles] = useState([]);
  const [leftFile, setLeftFile] = useState("");
  const [rightFile, setRightFile] = useState("");
  const [summary, setSummary] = useState(null);
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    loadMatchingSets();
  }, []);

  async function loadMatchingSets() {
    const response = await getMatchingSets();
    setMatchingSets(response.data.data || []);
  }

  async function handleSetChange(id) {
    setSelectedSet(id);
    setLeftFile("");
    setRightFile("");
    setSummary(null);

    if (!id) return;

    const response = await getFilesForMatchingSet(id);
    setLeftFiles(response.data.data.left_files || []);
    setRightFiles(response.data.data.right_files || []);
  }

  async function runReconciliation() {
    if (!selectedSet || !leftFile || !rightFile) {
      alert("Please select matching set, left file, and right file.");
      return;
    }

    setLoading(true);

    try {
      const response = await runSelectedReconciliation({
        batch_id: 1,
        matching_set_id: Number(selectedSet),
        left_file_id: Number(leftFile),
        right_file_id: Number(rightFile),
      });

      setSummary(response.data.data);

      const resultResponse = await getResults();
      setResults(resultResponse.data.data || []);
    } catch (error) {
      console.error(error);
      alert("Reconciliation failed.");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Reconciliation Workbench</h1>
        <p className="text-slate-500 mt-1">
          Select matching set, choose source files, and run reconciliation.
        </p>
      </div>

      <div className="bg-white rounded-xl shadow p-6">
        <label className="block text-sm font-semibold mb-2">
          Matching Set
        </label>

        <select
          value={selectedSet}
          onChange={(e) => handleSetChange(e.target.value)}
          className="w-full border rounded-lg px-4 py-3"
        >
          <option value="">Select Matching Set</option>
          {matchingSets.map((set) => (
            <option key={set.id} value={set.id}>
              {set.set_name}
            </option>
          ))}
        </select>
      </div>

      {selectedSet && (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <FilePanel
            title="Left Source Files"
            files={leftFiles}
            selected={leftFile}
            onSelect={setLeftFile}
          />

          <FilePanel
            title="Right Source Files"
            files={rightFiles}
            selected={rightFile}
            onSelect={setRightFile}
          />
        </div>
      )}

      {selectedSet && (
        <div className="flex justify-end">
          <button
            onClick={runReconciliation}
            disabled={loading}
            className="bg-blue-600 text-white px-8 py-3 rounded-lg"
          >
            {loading ? "Running..." : "Run Selected Reconciliation"}
          </button>
        </div>
      )}

      {summary && (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          <SummaryCard title="Matched" value={summary.matched} />
          <SummaryCard title="Exceptions" value={summary.exceptions} />
        </div>
      )}

      {summary && (
        <div className="bg-white rounded-xl shadow overflow-x-auto">
          <div className="p-5 border-b">
            <h2 className="font-bold text-xl">Latest Reconciliation Results</h2>
          </div>

          <table className="w-full text-sm">
            <thead className="bg-slate-100">
              <tr>
                <th className="p-4 text-left">Transaction ID</th>
                <th className="p-4 text-left">Status</th>
                <th className="p-4 text-left">Exception</th>
                <th className="p-4 text-left">Remarks</th>
              </tr>
            </thead>

            <tbody>
              {results.slice(0, 10).map((item) => (
                <tr key={item.id} className="border-b">
                  <td className="p-4 font-semibold">{item.transaction_id}</td>
                  <td className="p-4">{item.result_status}</td>
                  <td className="p-4">{item.exception_code || "-"}</td>
                  <td className="p-4">{item.remarks}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

function FilePanel({ title, files, selected, onSelect }) {
  return (
    <div className="bg-white rounded-xl shadow p-6">
      <h2 className="font-bold text-lg mb-4">{title}</h2>

      <div className="space-y-3">
        {files.map((file) => (
          <label
            key={file.id}
            className={`block border rounded-lg p-4 cursor-pointer ${
              Number(selected) === file.id
                ? "border-blue-600 bg-blue-50"
                : "border-slate-200"
            }`}
          >
            <input
              type="radio"
              name={title}
              value={file.id}
              checked={Number(selected) === file.id}
              onChange={(e) => onSelect(e.target.value)}
              className="mr-3"
            />

            <span className="font-semibold">{file.file_name}</span>

            <div className="text-sm text-slate-500 mt-2">
              Status: {file.status} | Records: {file.total_records} | Date:{" "}
              {file.business_date?.slice(0, 10)}
            </div>
          </label>
        ))}
      </div>
    </div>
  );
}

function SummaryCard({ title, value }) {
  return (
    <div className="bg-white rounded-xl shadow p-6">
      <p className="text-slate-500">{title}</p>
      <p className="text-3xl font-bold mt-2">{value}</p>
    </div>
  );
}

export default ReconciliationWorkbench;