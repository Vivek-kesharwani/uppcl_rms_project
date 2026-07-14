import api from "./api";

export const searchTransactions = (params = {}) =>
    api.get("/transactions/search", {
        params,
    });