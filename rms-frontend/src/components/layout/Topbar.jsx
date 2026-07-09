import { Bell, UserCircle } from "lucide-react";

function Topbar() {
  const user = JSON.parse(localStorage.getItem("user")) || {};

  return (
    <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-6 shadow-sm">
      <div>
        <h2 className="text-lg font-semibold text-slate-800">
          Reconciliation Management System
        </h2>
        <p className="text-xs text-slate-500">
          Secure financial reconciliation workspace
        </p>
      </div>

      <div className="flex items-center gap-4">
        <button className="rounded-full p-2 text-slate-500 hover:bg-slate-100">
          <Bell size={20} />
        </button>

        <div className="flex items-center gap-3">
          <UserCircle className="text-slate-600" size={34} />
          <div className="text-right">
            <p className="text-sm font-semibold text-slate-800">
              {user.name || "User"}
            </p>
            <p className="text-xs text-slate-500">
              {user.role || user.domain || "Role"}
            </p>
          </div>
        </div>
      </div>
    </header>
  );
}

export default Topbar;