import { useState } from "react";

function ExceptionResolveModal({ item, onClose, onResolve }) {
  const [resolutionNotes, setResolutionNotes] = useState("");
  const [rootCause, setRootCause] = useState("");

  if (!item) return null;

  return (
    <Modal title="Resolve Exception" onClose={onClose}>
      <div className="space-y-4">
        <textarea
          value={resolutionNotes}
          onChange={(e) => setResolutionNotes(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
          placeholder="Resolution Notes"
          rows={4}
        />

        <textarea
          value={rootCause}
          onChange={(e) => setRootCause(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
          placeholder="Root Cause"
          rows={3}
        />

        <button
          onClick={() =>
            onResolve(item.id, {
              resolution_notes: resolutionNotes,
              root_cause: rootCause,
            })
          }
          className="rounded-lg bg-green-600 px-6 py-3 font-semibold text-white"
        >
          Resolve
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

export default ExceptionResolveModal;