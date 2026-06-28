import { NavLink } from "react-router-dom";
import {
  FaTachometerAlt,
  FaUpload,
  FaHistory,
  FaExclamationTriangle,
  FaSearch,
  FaChartBar,
} from "react-icons/fa";

function Sidebar() {
  const user = JSON.parse(localStorage.getItem("user")) || {};
  const role = user.role;

  const menuItems = [
    {
      name: "Dashboard",
      path: "/dashboard",
      icon: <FaTachometerAlt />,
      roles: ["HQ_ADMIN", "DISCOM_ADMIN", "OPERATOR", "VIEWER"],
    },
    {
      name: "Upload Files",
      path: "/upload",
      icon: <FaUpload />,
      roles: ["HQ_ADMIN", "DISCOM_ADMIN", "OPERATOR"],
    },
    {
      name: "Upload History",
      path: "/upload-history",
      icon: <FaHistory />,
      roles: ["HQ_ADMIN", "DISCOM_ADMIN", "OPERATOR"],
    },
    {
      name: "Exceptions",
      path: "/exceptions",
      icon: <FaExclamationTriangle />,
      roles: ["HQ_ADMIN", "DISCOM_ADMIN", "OPERATOR"],
    },
    {
      name: "Transaction Search",
      path: "/search",
      icon: <FaSearch />,
      roles: ["HQ_ADMIN", "DISCOM_ADMIN", "OPERATOR", "VIEWER"],
    },
    {
      name: "Reports",
      path: "/reports",
      icon: <FaChartBar />,
      roles: ["HQ_ADMIN", "DISCOM_ADMIN", "OPERATOR", "VIEWER"],
    },
  ];

  const visibleItems = menuItems.filter((item) =>
    item.roles.includes(role)
  );

  return (
    <div className="w-64 bg-slate-900 text-white min-h-screen">
      <div className="text-2xl font-bold text-center py-6 border-b border-slate-700">
        RMS System
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