import api from "./api";

export const getFilesForMatchingSet = (matchingSetId) =>
  api.get(`/reconciliation/matching-sets/${matchingSetId}/files`);

export const runSelectedReconciliation = (payload) =>
  api.post("/reconciliation/run-selected", payload);

export const getReconciliationHistory = () =>
  api.get("/reconciliation/history");

export const getMatchingSets = () =>
    api.get("/reconciliation/matching-sets");