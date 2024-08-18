(function ($) {
  "use strict";

  class Publics {
    constructor() {
      const _this = this;
      this.searchTimeout;
      this.isSearching = false;

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
        function (e) {
          e.preventDefault();
          clearTimeout(_this.searchTimeout);
          _this.searchTimeout = setTimeout(() => {
            _this.updateList();
          }, 800);
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
      if (type == "ajax") {
        url = rhcArr.adminAjax;
        data = {
          nonce: rhcArr.nonce,
          action: "reisetopia_hotels_get_all",
          name,
          location,
          sorting,
          order,
          max_price,
          min_price,
          page,
        };
      } else if (type == "rest-api") {
        url = rhcArr.homeUrl + "/wp-json/reisetopia-hotels/v1/hotels/";
        data = JSON.stringify({
          name,
          location,
          sorting,
          order,
          max_price,
          min_price,
          page,
        });
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
            $(".hotel-list-container").html(
              _this.generateResultList(result.list)
            );
            $(".hotel-pagination-container").html(
              _this.generatePagination(result.page, result.maxNumPages, 3)
            );
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
        },
      });
    }

    generateResultList(list) {
      let html = "";
      for (const key in list) {
        const item = list[key];

        // Determine the image or fallback icon
        const img = item.img
          ? `<img src="${item.img}" class="tw-h-full tw-w-24 md:tw-w-36 tw-object-cover tw-bg-gray-300">`
          : `<div class="tw-h-full tw-w-24 md:tw-w-36 tw-flex-center tw-bg-gray-300"><i class="reisetopiaicon-gallery tw-text-4xl tw-text-white"></i></div>`;

        // Generate the rating stars
        let rate = "";
        for (let i = 1; i <= 5; i++) {
          rate += `<i class="reisetopiaicon-star${
            i <= Number(item.rate) ? "-fill" : ""
          } tw-text-yellow-300"></i>`;
        }

        // Construct the HTML for each hotel item
        html += `  
          <a href="${item.link}" class="hotel-item tw-rounded-md tw-border tw-border-solid tw-border-gray-300 tw-flex tw-text-gray-700 tw-overflow-hidden hover:tw-shadow-lg tw-transition-all">  
              ${img}  
              <div class="tw-p-4 tw-flex tw-flex-col tw-gap-2 tw-grow">  
                  <h3 class="tw-font-bold tw-text-lg tw-line-clamp-1 tw-text-black">${item.name}</h3>  
                  <div class="tw-font-bold">Location: <span class="tw-font-normal">${item.city}</span></div>  
                  <div class="tw-font-bold">Price: <span class="tw-font-normal">${item.priceRange.min}-${item.priceRange.max}</span></div>  
                  <div class="tw-font-bold">Rating: ${rate} <span class="">( ${item.rate} )</span></div>  
              </div>  
          </a>  
          `;
      }
      return html; // Return the generated HTML
    }

    generateLoading(selector, number = 6) {
      let skeleton = "";
      for (let i = 0; i < number; i++) {
        skeleton += `  
          <div class="tw-rounded-xl tw-border tw-border-solid tw-border-gray-300 tw-flex tw-text-gray-700 tw-overflow-hidden">  
              <div class="tw-h-full tw-w-24 md:tw-w-36 tw-flex-center tw-bg-gray-300 skeleton"></div>  
              <div class="tw-p-4 tw-flex tw-flex-col tw-gap-2 tw-grow">  
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

    generatePagination(currentPage, maxPages, paginationOffset) {
      // Create the pagination container
      const paginationContainer = $("<div/>", {
        class:
          "hotel-pagination tw-flex tw-w-full tw-pt-4 md:tw-pt-10 tw-text-sm",
      });
      if (maxPages > 1) {
        const innerContainer = $("<div/>", {
          class:
            "pagination-inner tw-mx-auto tw-flex tw-items-start tw-gap-1 md:tw-gap-3 tw-bg-white tw-rounded-md tw-py-2 tw-px-6",
        });

        // Previous page button
        if (currentPage > 1) {
          $("<div/>", {
            class:
              "page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer hover:tw-bg-green-1 hover:tw-text-white tw-transition-all",
            "data-page": currentPage - 1,
            html: '<i class="reisetopiaicon-arrow-left"></i>',
          }).appendTo(innerContainer);
        }

        // Generate page numbers
        if (maxPages > 6) {
          // First page
          $("<div/>", {
            class:
              "page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer tw-transition-all" +
              (currentPage === 1
                ? " tw-bg-green-1 tw-text-white selected"
                : " hover:tw-bg-green-1 hover:tw-text-white"),
            "data-page": 1,
            text: 1,
          }).appendTo(innerContainer);

          // Show ellipsis before
          if (currentPage > paginationOffset + 2) {
            $("<div/>", {
              class:
                "page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center hover:tw-bg-green-1 hover:tw-text-white tw-transition-all",
              text: "...",
            }).appendTo(innerContainer);
          }

          // Previous neighboring pages
          for (
            let i = Math.max(2, currentPage - paginationOffset);
            i <= Math.min(currentPage + paginationOffset, maxPages - 1);
            i++
          ) {
            if (i !== currentPage) {
              $("<div/>", {
                class:
                  "page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer tw-transition-all hover:tw-bg-green-1 hover:tw-text-white",
                "data-page": i,
                text: i,
              }).appendTo(innerContainer);
            } else {
              $("<div/>", {
                class:
                  "page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer tw-transition-all tw-bg-green-1 tw-text-white selected",
                "data-page": i,
                text: i,
              }).appendTo(innerContainer);
            }
          }

          // Show ellipsis after
          if (currentPage < maxPages - paginationOffset - 1) {
            $("<div/>", {
              class:
                "page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center   hover:tw-bg-green-1 hover:tw-text-white tw-transition-all",
              text: "...",
            }).appendTo(innerContainer);
          }

          // Last page
          $("<div/>", {
            class:
              "page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer tw-transition-all" +
              (currentPage === maxPages
                ? " tw-bg-green-1 tw-text-white selected"
                : " hover:tw-bg-green-1 hover:tw-text-white"),
            "data-page": maxPages,
            text: maxPages,
          }).appendTo(innerContainer);
        } else {
          // For maxPages less than or equal to 6
          for (let i = 1; i <= maxPages; i++) {
            $("<div/>", {
              class:
                "page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer tw-transition-all" +
                (i === currentPage
                  ? " tw-bg-green-1 tw-text-white selected"
                  : " hover:tw-bg-green-1 hover:tw-text-white"),
              "data-page": i,
              text: i,
            }).appendTo(innerContainer);
          }
        }

        // Next page button
        if (currentPage < maxPages) {
          $("<div/>", {
            class:
              "page-item tw-size-6 md:tw-size-8 tw-rounded-md tw-flex-center tw-cursor-pointer hover:tw-bg-green-1 hover:tw-text-white tw-transition-all",
            "data-page": currentPage + 1,
            html: '<i class="reisetopiaicon-arrow-left tw-rotate-180"></i>',
          }).appendTo(innerContainer);
        }

        paginationContainer.append(innerContainer);
      }
      return paginationContainer;
    }
  }
  const publics = new Publics();
})(jQuery);
