import api from "./api";

export const getOverview = () => api.get("/dashboard/overview");
export const getFiles = () => api.get("/dashboard/files");
export const getBatches = () => api.get("/dashboard/batches");
export const getResults = () => api.get("/dashboard/results");
export const getDashboardExceptions = () => api.get("/dashboard/exceptions");
export const getCharts = () => api.get("/dashboard/charts");