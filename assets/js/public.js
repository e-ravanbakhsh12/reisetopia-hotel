(function ($) {
  "use strict";

  class Publics {
    constructor() {
      this.searchTimeout;
      this.isSearching = false;

      // Event listeners
      this.initializeEventListeners();
    }

    initializeEventListeners() {
      const _this = this;
      $(document).on(
        "change",
        "#hotel-data-source, #hotel-sorting, #hotel-order ",
        function (e) {
          _this.updateList();
        }
      );
      $(document).on(
        "change keyup pest",
        "#hotel-name, #hotel-location, #hotel-min-price, #hotel-max-price",
        (e) => {
          e.preventDefault();
          clearTimeout(_this.searchTimeout);
          _this.searchTimeout = setTimeout(() => _this.updateList(), 800);
        }
      );

      $(document).on("click", ".page-item", function (e) {
        _this.updateList($(this).data("page"));
      });
    }

    updateList(page = 1) {
      const _this = this;
      if (_this.isSearching) return;
      $(".hotel-filter-container input,.hotel-filter-container select")
        .prop("disabled", true)
        .addClass("tw-opacity-60");
      $(".pagination-inner").addClass("skeleton");
      $(".pagination-inner .page-item").addClass("tw-invisible");
      _this.isSearching = true;
      $(".error-box").addClass("tw-hidden");
      const type = $("#hotel-data-source").val();
      const name = $("#hotel-name").val();
      const sorting = $("#hotel-sorting").val();
      const max_price = $("#hotel-max-price").val();
      const min_price = $("#hotel-min-price").val();
      const order = $("#hotel-order").val();
      const location = $("#hotel-location").val();
      let data;
      let url;
      const params = {
        name,
        location,
        sorting,
        order,
        max_price,
        min_price,
        page,
      };
      if (type == "ajax") {
        url = rhcArr.adminAjax;
        data = {
          ...params,
          nonce: rhcArr.nonce,
          action: "reisetopia_hotels_get_all",
        };
      } else if (type == "rest-api") {
        url = rhcArr.homeUrl + "/wp-json/reisetopia-hotels/v1/hotels/";
        data = JSON.stringify(params);
      } else if (type == "rewrite-api") {
        url = rhcArr.homeUrl + "/wp-ajax/reisetopia-hotels/v1/hotels/";
        data = { ...params, nonce: rhcArr.nonce };
      } else {
        return;
      }
      _this.generateLoading(".hotel-list-container");
      $.ajax({
        url,
        data,
        dataType: "json",
        type: "POST",
        success: function (xhr, status, response) {
          if (response.status == 200 && response.responseJSON.value) {
            const result = response.responseJSON.value;
            $(".hotel-list-container").html(result.list);
            $(".hotel-pagination-container").html(result.pagination);
          }
        },
        error: function (xhr, status, error) {
          $(".error-box").removeClass("tw-hidden");
          $(".error-box .message-content").html(
            xhr.responseJSON?.message || "unknown error happen please try again"
          );
          $(".hotel-list-container").html("");
          $(".hotel-pagination-container").html("");
        },
        complete: function () {
          _this.isSearching = false;
          $(".hotel-filter-container input,.hotel-filter-container select")
            .prop("disabled", false)
            .removeClass("tw-opacity-60");
          $(".pagination-inner").removeClass("skeleton");
          $(".pagination-inner .page-item").removeClass("tw-invisible");
          _this.addNewUrl(params);
          window.dispatchEvent(
            new CustomEvent("queryLoaded", {
              detail: { container: $(".hotel-list-container").get(0) },
            })
          );
        },
      });
    }

    generateLoading(selector, number = 10) {
      let skeleton = "";
      for (let i = 0; i < number; i++) {
        skeleton += `  
          <div class="tw-rounded-xl tw-border tw-border-solid tw-border-gray-300 tw-flex tw-text-gray-700 tw-overflow-hidden">  
              <div class="tw-h-full tw-w-24 md:tw-w-36 tw-flex-center tw-bg-gray-300 skeleton"></div>  
              <div class="tw-p-4 tw-flex tw-flex-col tw-gap-4 tw-grow">  
                  <div class="tw-w-4/5 tw-h-6 tw-rounded-lg skeleton"></div>  
                  <div class="tw-w-3/4 tw-h-4 tw-rounded-lg skeleton"></div>  
                  <div class="tw-w-1/3 tw-h-4 tw-rounded-lg skeleton"></div>  
                  <div class="tw-w-1/2 tw-h-4 tw-rounded-lg skeleton"></div>  
              </div>  
          </div>  
          `;
      }
      $(selector).html(skeleton);
    }

    addNewUrl({ name, location, sorting, order, max_price, min_price, page }) {
      const params = {};

      if (page > 1) {
        params.pg = page;
      }
      if (name.length) {
        params["search-name"] = name;
      }
      if (location.length) {
        params.location = location;
      }
      if (sorting !== "date") {
        params.sorting = sorting;
      }
      if (order !== "DESC") {
        params.order = order;
      }
      if (max_price < 1000) {
        params["max-price"] = max_price;
      }
      if (min_price > 0) {
        params["min-price"] = min_price;
      }

      if (Object.keys(params).length) {
        const baseURL = window.location.href.split("?")[0];
        const queryString = new URLSearchParams(params).toString();

        const newUrl = `${baseURL}?${queryString}`;

        if (history.pushState) {
          history.pushState(null, null, newUrl);
        } else {
          window.location.href = newUrl;
        }
      }
    }

    addDataToFormData(params) {
      const formData = new FormData();

      for (const key in params) {
        formData.append(key, params[key]);
      }
      return formData;
    }
  }
  const publics = new Publics();
})(jQuery);
