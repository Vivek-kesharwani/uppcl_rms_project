import { useState } from "react";

function ExceptionAssignModal({ item, onClose, onAssign }) {
  const [assignedRole, setAssignedRole] = useState("DISCOM_OPERATOR");
  const [assignedTo, setAssignedTo] = useState("");

  if (!item) return null;

  return (
    <Modal title="Assign Exception" onClose={onClose}>
      <div className="space-y-4">
        <input
          value={assignedRole}
          onChange={(e) => setAssignedRole(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
          placeholder="Assigned Role"
        />

        <input
          value={assignedTo}
          onChange={(e) => setAssignedTo(e.target.value)}
          className="w-full rounded-lg border px-4 py-3"
          placeholder="Assigned To"
        />

        <button
          onClick={() =>
            onAssign(item.id, {
              assigned_role: assignedRole,
              assigned_to: assignedTo,
            })
          }
          className="rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white"
        >
          Assign
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

export default ExceptionAssignModal;