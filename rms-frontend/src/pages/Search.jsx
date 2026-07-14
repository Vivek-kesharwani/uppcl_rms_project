import { useEffect, useMemo, useState } from "react";

import { searchTransactions } from "../services/searchService";
import { getMatchingSets } from "../services/reconciliationService";

import PageHeader from "../components/common/PageHeader";
import LoadingSpinner from "../components/common/LoadingSpinner";
import EmptyState from "../components/common/EmptyState";

import SearchSummaryCards from "../components/search/SearchSummaryCards";
import SearchToolbar from "../components/search/SearchToolbar";
import SearchFilters from "../components/search/SearchFilters";
import SearchResultsTable from "../components/search/SearchResultsTable";
import TransactionDetailsModal from "../components/search/TransactionDetailsModal";
import SearchPagination from "../components/search/SearchPagination";

const initialFilters = {
  keyword: "",
  searchType: "ALL",
  businessDate: "",
  matchingSet: "",
  resultStatus: "",
  amount: "",
};

const ITEMS_PER_PAGE = 20;

function Search() {
  const [matchingSets, setMatchingSets] = useState([]);
  const [results, setResults] = useState([]);
  const [filters, setFilters] = useState(initialFilters);

  const [selectedTransaction, setSelectedTransaction] = useState(null);

  const [loadingSets, setLoadingSets] = useState(true);
  const [searching, setSearching] = useState(false);
  const [hasSearched, setHasSearched] = useState(false);

  const [error, setError] = useState("");

  const [currentPage, setCurrentPage] = useState(1);

  useEffect(() => {
    loadMatchingSets();
  }, []);

  async function loadMatchingSets() {
    try {
      setLoadingSets(true);
      setError("");

      const response = await getMatchingSets();
      const payload = response.data?.data;

      setMatchingSets(Array.isArray(payload) ? payload : []);
    } catch (err) {
      console.error("Matching set API error:", err);
      setMatchingSets([]);
      setError("Unable to load matching sets.");
    } finally {
      setLoadingSets(false);
    }
  }

  function buildSearchParams() {
    const params = {};

    const keyword = filters.keyword.trim();

    if (keyword) {
      switch (filters.searchType) {
        case "TRANSACTION_ID":
          params.transaction_id = keyword;
          break;

        case "CONSUMER_NUMBER":
          params.consumer_number = keyword;
          break;

        case "ACCOUNT_NUMBER":
          params.account_number = keyword;
          break;

        case "UTR_NUMBER":
          params.utr_number = keyword;
          break;

        case "SETTLEMENT_REFERENCE":
          params.settlement_ref = keyword;
          break;

        default:
          params.search = keyword;
          break;
      }
    }

    if (filters.businessDate) {
      params.business_date = filters.businessDate;
    }

    if (filters.matchingSet) {
      params.matching_set_id = filters.matchingSet;
    }

    if (filters.resultStatus) {
      params.result_status = filters.resultStatus;
    }

    if (filters.amount !== "") {
      params.amount = filters.amount;
    }

    return params;
  }

  function extractResultList(response) {
    const payload = response.data?.data;

    if (Array.isArray(payload)) {
      return payload;
    }

    if (Array.isArray(payload?.data)) {
      return payload.data;
    }

    if (Array.isArray(payload?.results)) {
      return payload.results;
    }

    if (Array.isArray(response.data?.results)) {
      return response.data.results;
    }

    return [];
  }

  async function handleSearch() {
    try {
      setSearching(true);
      setError("");
      setHasSearched(true);
      setCurrentPage(1);
      setSelectedTransaction(null);

      const params = buildSearchParams();
      const response = await searchTransactions(params);

      setResults(extractResultList(response));
    } catch (err) {
      console.error("Transaction search error:", err);

      setResults([]);

      setError(
        err.response?.data?.message ||
          "Transaction search failed. Please check the filters and try again."
      );
    } finally {
      setSearching(false);
    }
  }

  async function handleRefresh() {
    if (hasSearched) {
      await handleSearch();
      return;
    }

    await loadMatchingSets();
  }

  function handleClear() {
    setFilters(initialFilters);
    setResults([]);
    setCurrentPage(1);
    setHasSearched(false);
    setSelectedTransaction(null);
    setError("");
  }

  function escapeCSVValue(value) {
    if (value === null || value === undefined) {
      return "";
    }

    const stringValue = String(value).replaceAll('"', '""');

    return `"${stringValue}"`;
  }

  function handleExport() {
    if (results.length === 0) {
      setError("There are no search results to export.");
      return;
    }

    const headers = [
      "Transaction ID",
      "Consumer Number",
      "Account Number",
      "Amount",
      "Business Date",
      "Matching Set",
      "Result Status",
      "Exception Code",
      "Batch Code",
    ];

    const rows = results.map((item) => [
      item.transaction_id,
      item.consumer_number,
      item.account_number,
      item.amount,
      item.business_date?.substring(0, 10),
      item.matching_set?.set_name || item.matching_set_name,
      item.result_status,
      item.exception_code,
      item.batch?.batch_code || item.batch_code,
    ]);

    const csvContent = [
      headers.map(escapeCSVValue).join(","),
      ...rows.map((row) => row.map(escapeCSVValue).join(",")),
    ].join("\n");

    const blob = new Blob([csvContent], {
      type: "text/csv;charset=utf-8;",
    });

    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");

    link.href = url;
    link.download = `transaction_search_${new Date()
      .toISOString()
      .substring(0, 10)}.csv`;

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    URL.revokeObjectURL(url);
  }

  const totalPages = Math.max(
    1,
    Math.ceil(results.length / ITEMS_PER_PAGE)
  );

  const paginatedResults = useMemo(() => {
    const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
    const endIndex = startIndex + ITEMS_PER_PAGE;

    return results.slice(startIndex, endIndex);
  }, [results, currentPage]);

  function handlePageChange(page) {
    const safePage = Math.min(Math.max(page, 1), totalPages);

    setCurrentPage(safePage);

    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  }

  if (loadingSets) {
    return <LoadingSpinner text="Loading transaction search..." />;
  }

    return (
    <div className="space-y-6">
      <PageHeader
        title="Enterprise Transaction Search"
        description="Search, review and analyze reconciled utility payment transactions."
      />

      {error && (
        <div className="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
          {error}
        </div>
      )}

      <SearchSummaryCards transactions={results} />

      <SearchToolbar
        totalResults={results.length}
        onRefresh={handleRefresh}
        onExport={handleExport}
        onClear={handleClear}
      />

      <SearchFilters
        matchingSets={matchingSets}
        filters={filters}
        setFilters={setFilters}
        onSearch={handleSearch}
        onReset={handleClear}
      />

      {!hasSearched ? (
        <EmptyState
          title="Start Your Search"
          message="Select filters and click Search to retrieve transactions."
        />
      ) : (
        <>
          {searching ? (
            <LoadingSpinner text="Searching transactions..." />
          ) : (
            <>
              <SearchResultsTable
                results={paginatedResults}
                onView={setSelectedTransaction}
              />

              <SearchPagination
                currentPage={currentPage}
                totalPages={totalPages}
                onPageChange={handlePageChange}
              />
            </>
          )}
        </>
      )}

      <TransactionDetailsModal
        transaction={selectedTransaction}
        onClose={() => setSelectedTransaction(null)}
      />
    </div>
  );
}

export default Search;