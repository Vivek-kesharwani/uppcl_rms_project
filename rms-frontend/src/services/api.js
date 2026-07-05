import axios from "axios";

const api = axios.create({
    baseURL: "http://127.0.0.1:8000/api",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

export const login = (credentials) =>
    api.post("/login", credentials);

export const me = () =>
    api.get("/me");

/*
|--------------------------------------------------------------------------
| Dashboard APIs
|--------------------------------------------------------------------------
*/

export const getOverview = () =>
    api.get("/dashboard/overview");

export const getFiles = () =>
    api.get("/dashboard/files");

export const getBatches = () =>
    api.get("/dashboard/batches");

export const getResults = () =>
    api.get("/dashboard/results");

export const getExceptions = () =>
    api.get("/dashboard/exceptions");

export const getCharts = () =>
    api.get("/dashboard/charts");

/*
|--------------------------------------------------------------------------
| Reconciliation
|--------------------------------------------------------------------------
*/

export const runBatch = (batchId) =>
    api.post(`/reconciliation/run/${batchId}`);

/*
|--------------------------------------------------------------------------
| Upload APIs
|--------------------------------------------------------------------------
*/

export const uploadFile = (formData) =>
    api.post("/upload", formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });

export const getUploads = () =>
    api.get("/uploads");

export const getUploadHistory = () =>
    api.get("/upload-history");

/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/

export const getDailyReport = () =>
    api.get("/reports/daily-reconciliation");

export const getExceptionReport = () =>
    api.get("/reports/exception-summary");

export const getSettlementReport = () =>
    api.get("/reports/settlement-summary");

export default api;

export const getMatchingSets = () =>
    api.get("/reconciliation/matching-sets");

export const getFilesForMatchingSet = (matchingSetId) =>
    api.get(`/reconciliation/matching-sets/${matchingSetId}/files`);

export const runSelectedReconciliation = (payload) =>
    api.post("/reconciliation/run-selected", payload);