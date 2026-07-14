import { useState } from "react";

function ExceptionEditModal({ item, onClose, onSave }) {
  const [remarks, setRemarks] = useState(item?.remarks || "");
  const [priority, setPriority] = useState(item?.priority || "MEDIUM");
  const [rootCause, setRootCause] = useState(item?.root_cause || "");

  if (!item) return null;

  const submit = () => {
    onSave(item.id, {
      remarks,
      priority,
      root_cause: rootCause,
    });
  };

  return (
    <Modal title="Edit Exception" onClose={onClose}>
      <div className="space-y-4">
        <select
          value={priority}
          onChange={(e) => setPriority(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
        >
          <option value="LOW">Low</option>
          <option value="MEDIUM">Medium</option>
          <option value="HIGH">High</option>
          <option value="CRITICAL">Critical</option>
        </select>

        <textarea
          value={remarks}
          onChange={(e) => setRemarks(e.target.value)}
          placeholder="Remarks"
          className="w-full rounded-lg border px-4 py-3"
          rows={3}
        />

        <textarea
          value={rootCause}
          onChange={(e) => setRootCause(e.target.value)}
          placeholder="Root Cause"
          className="w-full rounded-lg border px-4 py-3"
          rows={3}
        />

        <button
          onClick={submit}
          className="rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white"
        >
          Save Changes
        </button>
      </div>
    </Modal>
  );
}

function Modal({ title, children, onClose }) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
      <div className="w-full max-w-xl rounded-xl bg-white shadow-xl">
        <div className="flex items-center justify-between border-b p-5">
          <h2 className="text-xl font-bold">{title}</h2>
          <button onClick={onClose}>✕</button>
        </div>
        <div className="p-6">{children}</div>
      </div>
    </div>
  );
}

export default ExceptionEditModal;