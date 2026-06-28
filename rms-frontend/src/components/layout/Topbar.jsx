import { FaUserCircle } from "react-icons/fa";

function Topbar() {
  return (
    <div className="h-16 bg-white shadow flex justify-between items-center px-6">
      <h1 className="text-2xl font-semibold text-slate-800">
        UPPCL Reconciliation Management System
      </h1>

      <div className="flex items-center gap-3">
        <FaUserCircle className="text-3xl text-slate-700" />
        <div>
          <div className="font-semibold">HQ Admin</div>
          <div className="text-sm text-gray-500">Administrator</div>
        </div>
      </div>
    </div>
  );
}

export default Topbar;