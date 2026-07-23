document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById('globalSearch');
  if (!searchInput) return;

  const searchWrapper = searchInput.parentElement;
  if (searchWrapper) {
    searchWrapper.style.position = "relative";
  }

  const resultsDropdown = document.createElement("div");
  resultsDropdown.id = "live-search-dropdown";
  resultsDropdown.className = "live-search-dropdown";
  resultsDropdown.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        max-height: 380px;
        overflow-y: auto;
        display: none;
        z-index: 1000;
        margin-top: 6px;
        padding: 10px;
    `;
  searchWrapper.appendChild(resultsDropdown);

  let debounceTimer;

  searchInput.addEventListener("input", function () {
    clearTimeout(debounceTimer);
    const query = this.value.trim();

    if (query.length < 2) {
      resultsDropdown.style.display = "none";
      resultsDropdown.innerHTML = "";
      return;
    }

    debounceTimer = setTimeout(() => {
      fetchLiveResults(query);
    }, 300);
  });

  function fetchLiveResults(query) {
    // Utilize absolute path mapping via root location or relative fallback
    fetch(`http://localhost/php%20+20larevel/TEAM3-library_management/search/search_ajax.php?q=${encodeURIComponent(query)}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          renderResults(data.results, query);
        }
      })
      .catch((err) => console.error("Search AJAX Error:", err));
  }

  function renderResults(results, query) {
    const { books, members, borrowings } = results;
    let html = "";
    const totalResults = books.length + members.length + borrowings.length;

    if (totalResults === 0) {
      resultsDropdown.innerHTML = `<div style="padding: 10px; text-align: center; color: #64748b; font-size: 13px;">No results found for "<strong>${query}</strong>"</div>`;
      resultsDropdown.style.display = "block";
      return;
    }

    if (books.length > 0) {
      html += `<div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 6px 0 4px 6px;">Books</div>`;
      books.forEach((book) => {
        html += `
            <a href="http://localhost/php%20+20larevel/TEAM3-library_management/Books/index.php" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; text-decoration: none; color: #1e293b; border-radius: 6px; font-size: 13px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                <div>
                    <strong>${book.title}</strong>
                    <span style="font-size: 11px; color: #64748b; display: block;">Author: ${book.author_name || "N/A"}</span>
                </div>
                <span style="font-size: 10px; padding: 2px 6px; border-radius: 4px; background: #e2e8f0; color: #475569;">${book.status}</span>
            </a>`;
      });
    }

    if (members.length > 0) {
      html += `<div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 10px 0 4px 6px;">Students / Members</div>`;
      members.forEach((m) => {
        html += `
            <a href="http://localhost/php%20+20larevel/TEAM3-library_management/members/index.php" style="display: flex; align-items: center; gap: 10px; padding: 8px 10px; text-decoration: none; color: #1e293b; border-radius: 6px; font-size: 13px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                <i class="fa-solid fa-user" style="color: #6366f1;"></i>
                <div>
                    <strong>${m.name}</strong>
                    <span style="font-size: 11px; color: #64748b; display: block;">${m.email || m.phone}</span>
                </div>
            </a>`;
      });
    }

    if (borrowings.length > 0) {
      html += `<div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 10px 0 4px 6px;">Transactions</div>`;
      borrowings.forEach((b) => {
        html += `
            <a href="http://localhost/php%20+20larevel/TEAM3-library_management/Borrow/index.php" style="display: block; padding: 8px 10px; text-decoration: none; color: #1e293b; border-radius: 6px; font-size: 13px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                <div><strong>${b.student_name}</strong> borrowed <em>${b.book_title}</em></div>
                <span style="font-size: 11px; color: #64748b;">Status: ${b.status}</span>
            </a>`;
      });
    }

    resultsDropdown.innerHTML = html;
    resultsDropdown.style.display = "block";
  }

  document.addEventListener("click", function (e) {
    if (!searchInput.contains(e.target) && !resultsDropdown.contains(e.target)) {
      resultsDropdown.style.display = "none";
    }
  });
});