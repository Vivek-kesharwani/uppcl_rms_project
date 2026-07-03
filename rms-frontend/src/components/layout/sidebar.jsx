import { NavLink } from "react-router-dom";
import {
  FaTachometerAlt,
  FaUpload,
  FaHistory,
  FaExclamationTriangle,
  FaSearch,
  FaChartBar,
  FaCogs,
} from "react-icons/fa";

function Sidebar() {
  const user = JSON.parse(localStorage.getItem("user")) || {};
  const domain = user.domain;

  const menuItems = [
    {
      name: "HQ Dashboard",
      path: "/hq/dashboard",
      icon: <FaTachometerAlt />,
      domains: ["HQ"],
    },
    {
      name: "DISCOM Dashboard",
      path: "/discom/dashboard",
      icon: <FaTachometerAlt />,
      domains: ["DISCOM"],
    },
    {
      name: "Agency Dashboard",
      path: "/agency/dashboard",
      icon: <FaTachometerAlt />,
      domains: ["AGENCY"],
    },
    {
      name: "Agency Upload",
      path: "/upload",
      icon: <FaUpload />,
      domains: ["HQ", "AGENCY"],
    },
    {
      name: "File Monitor",
      path: "/upload-history",
      icon: <FaHistory />,
      domains: ["HQ", "AGENCY"],
    },
    {
      name: "Reconciliation",
      path: "/reconciliation",
      icon: <FaCogs />,
      domains: ["HQ"],
    },
    {
      name: "Transaction Search",
      path: "/search",
      icon: <FaSearch />,
      domains: ["HQ", "DISCOM"],
    },
    {
      name: "Exceptions",
      path: "/exceptions",
      icon: <FaExclamationTriangle />,
      domains: ["HQ", "DISCOM"],
    },
    {
      name: "Reports",
      path: "/reports",
      icon: <FaChartBar />,
      domains: ["HQ", "DISCOM"],
    },
  ];

  const visibleItems = menuItems.filter((item) =>
    item.domains.includes(domain)
  );

  return (
    <div className="w-64 bg-slate-900 text-white min-h-screen">
      <div className="text-2xl font-bold text-center py-6 border-b border-slate-700">
        RMS System
      </div>

      <div className="px-4 py-3 border-b border-slate-700 text-sm text-slate-300">
        <div className="font-semibold text-white">{user.name || "User"}</div>
        <div>{domain || "Domain"}</div>
      </div>

      <div className="mt-4">
        {visibleItems.map((item) => (
          <NavLink
            key={item.path}
            to={item.path}
            className={({ isActive }) =>
              `flex items-center gap-3 px-6 py-3 hover:bg-slate-700 ${
                isActive ? "bg-blue-600" : ""
              }`
            }
          >
            {item.icon}
            {item.name}
          </NavLink>
        ))}
      </div>
    </div>
  );
}

export default Sidebar;