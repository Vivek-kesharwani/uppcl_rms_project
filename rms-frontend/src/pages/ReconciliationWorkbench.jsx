import { useEffect, useState } from "react";
import {
  getMatchingSets,
  getFilesForMatchingSet,
  runSelectedReconciliation,
} from "../services/reconciliationService";

import PageHeader from "../components/common/PageHeader";
import LoadingSpinner from "../components/common/LoadingSpinner";
import FileSelector from "../components/reconciliation/FileSelector";
import BatchDetailsCard from "../components/reconciliation/BatchDetailsCard";
import ReconciliationSummaryCard from "../components/reconciliation/ReconciliationSummaryCard";

function ReconciliationWorkbench() {
  const [matchingSets, setMatchingSets] = useState([]);
  const [selectedSet, setSelectedSet] = useState("");

  const [leftFiles, setLeftFiles] = useState([]);
  const [rightFiles, setRightFiles] = useState([]);

  const [leftFile, setLeftFile] = useState("");
  const [rightFile, setRightFile] = useState("");

  const [summary, setSummary] = useState(null);
  const [loadingSets, setLoadingSets] = useState(true);
  const [loadingFiles, setLoadingFiles] = useState(false);
  const [running, setRunning] = useState(false);
  const [error, setError] = useState("");

  useEffect(() => {
    loadMatchingSets();
  }, []);

  async function loadMatchingSets() {
    try {
      setLoadingSets(true);
      const response = await getMatchingSets();
      setMatchingSets(response.data.data || []);
    } catch (err) {
      console.error(err);
      setError("Failed to load matching sets.");
    } finally {
      setLoadingSets(false);
    }
  }

  async function handleSetChange(id) {
    setSelectedSet(id);
    setLeftFile("");
    setRightFile("");
    setLeftFiles([]);
    setRightFiles([]);
    setSummary(null);
    setError("");

    if (!id) return;

    try {
      setLoadingFiles(true);

      const response = await getFilesForMatchingSet(id);

      const data = response.data.data;

      setLeftFiles(data.left_files || []);
      setRightFiles(data.right_files || []);

      if (data.recommended_pair) {
        setLeftFile(String(data.recommended_pair.left_file_id));
        setRightFile(String(data.recommended_pair.right_file_id));
      }
    } catch (err) {
      console.error(err);
      setError("Failed to load files for selected matching set.");
    } finally {
      setLoadingFiles(false);
    }
  }

  async function runReconciliation() {
    if (!selectedSet || !leftFile || !rightFile) {
      setError("Please select matching set, left file and right file.");
      return;
    }

    try {
      setRunning(true);
      setError("");
      setSummary(null);

      const response = await runSelectedReconciliation({
        matching_set_id: Number(selectedSet),
        left_file_id: Number(leftFile),
        right_file_id: Number(rightFile),
      });

      setSummary(response.data.data);
    } catch (err) {
      console.error(err);
      setError(
        err.response?.data?.message ||
          "Reconciliation failed. Please check selected files."
      );
    } finally {
      setRunning(false);
    }
  }

  if (loadingSets) {
    return <LoadingSpinner text="Loading reconciliation workbench..." />;
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Reconciliation Workbench"
        description="Select matching set, filter source files and run reconciliation."
      />

      {error && (
        <div className="rounded-lg border border-red-300 bg-red-50 p-4 text-sm text-red-700">
          {error}
        </div>
      )}

      <div className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <label className="mb-2 block text-sm font-semibold text-slate-700">
          Matching Set
        </label>

        <select
          value={selectedSet}
          onChange={(e) => handleSetChange(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
        >
          <option value="">Select Matching Set</option>

          {matchingSets.map((set) => (
            <option key={set.id} value={set.id}>
              {set.set_name}
            </option>
          ))}
        </select>
      </div>

      {loadingFiles && <LoadingSpinner text="Loading matching files..." />}

      {selectedSet && !loadingFiles && (
        <div className="grid grid-cols-1 gap-6 xl:grid-cols-2">
          <FileSelector
            title="Left Source File"
            files={leftFiles}
            selectedFileId={leftFile}
            onSelect={setLeftFile}
          />

          <FileSelector
            title="Right Source File"
            files={rightFiles}
            selectedFileId={rightFile}
            onSelect={setRightFile}
          />
        </div>
      )}

      {selectedSet && !loadingFiles && (
        <div className="flex justify-center">
          <button
            onClick={runReconciliation}
            disabled={running || !selectedSet || !leftFile || !rightFile}
            className="rounded-lg bg-blue-600 px-10 py-3 font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {running ? "Running Reconciliation..." : "Run Reconciliation"}
          </button>
        </div>
      )}

      {summary && (
        <div className="space-y-6">
          <BatchDetailsCard summary={summary} />
          <ReconciliationSummaryCard summary={summary} />
        </div>
      )}
    </div>
  );
}

export default ReconciliationWorkbench;