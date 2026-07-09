import { Outlet } from "react-router-dom";
import Sidebar from "./Sidebar";
import Topbar from "./Topbar";

function DashboardLayout() {
  return (
    <div className="min-h-screen bg-slate-100">
      <Sidebar />

      <div className="min-h-screen lg:pl-72">
        <Topbar />

        <main className="p-6">
          <div className="mx-auto max-w-7xl">
            <Outlet />
          </div>
        </main>
      </div>
    </div>
  );
}

export default DashboardLayout;