import { NavLink, useNavigate } from "react-router-dom";
import {
  AlertTriangle,
  BarChart3,
  FileText,
  FolderClock,
  LayoutDashboard,
  LogOut,
  RefreshCw,
  Search,
  ShieldCheck,
  Upload,
} from "lucide-react";

function Sidebar() {
  const navigate = useNavigate();
  const user = JSON.parse(localStorage.getItem("user")) || {};
  const domain = user.domain;

  const dashboardPath =
    domain === "HQ"
      ? "/hq/dashboard"
      : domain === "DISCOM"
      ? "/discom/dashboard"
      : domain === "AGENCY"
      ? "/agency/dashboard"
      : "/dashboard";

  const menuItems = [
    { name: "Dashboard", path: dashboardPath, icon: LayoutDashboard, domains: ["HQ", "DISCOM", "AGENCY"] },
    { name: "Upload Center", path: "/upload", icon: Upload, domains: ["HQ", "AGENCY"] },
    { name: "File Monitor", path: "/upload-history", icon: FolderClock, domains: ["HQ", "DISCOM", "AGENCY"] },
    { name: "Reconciliation", path: "/reconciliation", icon: RefreshCw, domains: ["HQ"] },
    { name: "Result Repository", path: "/results", icon: FileText, domains: ["HQ", "DISCOM"] },
    { name: "Exceptions", path: "/exceptions", icon: AlertTriangle, domains: ["HQ", "DISCOM"] },
    { name: "Reports", path: "/reports", icon: BarChart3, domains: ["HQ", "DISCOM"] },
    { name: "Search", path: "/search", icon: Search, domains: ["HQ", "DISCOM"] },
    { name: "Audit Logs", path: "/audit-logs", icon: ShieldCheck, domains: ["HQ"] },
  ];

  const visibleItems = menuItems.filter((item) => item.domains.includes(domain));

  const logout = () => {
    localStorage.clear();
    navigate("/");
  };

  return (
    <aside className="fixed inset-y-0 left-0 z-40 hidden w-72 bg-slate-950 text-white lg:block">
      <div className="flex h-16 items-center border-b border-slate-800 px-6">
        <div>
          <h1 className="text-xl font-bold">UPPCL RMS</h1>
          <p className="text-xs text-slate-400">Reconciliation System</p>
        </div>
      </div>

      <div className="border-b border-slate-800 px-6 py-4">
        <p className="text-sm font-semibold">{user.name || "User"}</p>
        <p className="text-xs text-slate-400">{user.role || domain || "Role"}</p>
      </div>

      <nav className="space-y-1 px-3 py-4">
        {visibleItems.map((item) => {
          const Icon = item.icon;

          return (
            <NavLink
              key={item.path}
              to={item.path}
              className={({ isActive }) =>
                `flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition ${
                  isActive
                    ? "bg-blue-600 text-white"
                    : "text-slate-300 hover:bg-slate-800 hover:text-white"
                }`
              }
            >
              <Icon size={18} />
              {item.name}
            </NavLink>
          );
        })}
      </nav>

      <div className="absolute bottom-0 w-full border-t border-slate-800 p-3">
        <button
          onClick={logout}
          className="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-red-300 hover:bg-red-950"
        >
          <LogOut size={18} />
          Logout
        </button>
      </div>
    </aside>
  );
}

export default Sidebar;