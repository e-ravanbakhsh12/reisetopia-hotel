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
    }

    updateList(page = 1) {
      const _this = this;
      if (_this.isSearching) return;
      $(".hotel-filter-container input,.hotel-filter-container select")
        .prop("disabled", true)
        .addClass("tw-opacity-60");
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
        };
      } else if (type == "rest-api") {
        url = rhcArr.homeUrl + "/wp-json/reisetopia-hotels/v1/hotels/";
        data = JSON.stringify({ name, location, sorting, order,max_price,min_price, });
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
            $(".hotel-list-container").html(
              _this.generateResultList(response.responseJSON.value)
            );
          }
        },
        error: function (xhr, status, error) {
          $(".error-box").removeClass("tw-hidden");
          $(".error-box .message-content").html(
            xhr.responseJSON?.message ||
              "unknown error happen please try again"
          );
          $(".hotel-list-container").html("");
        },
        complete: function () {
          _this.isSearching = false;
          $(".hotel-filter-container input,.hotel-filter-container select")
            .prop("disabled", false)
            .removeClass("tw-opacity-60");
        },
      });
    }

    generateResultList(list) {
      let html = "";
      for (const key in list) {
        const img = list[key].img
          ? `<img src="${list[key].img}" class="tw-h-full tw-w-24 md:tw-w-36 tw-object-cover tw-bg-gray-300">`
          : `<div class="tw-h-full tw-w-24 md:tw-w-36 tw-flex-center tw-bg-gray-300"><i class="reisetopiaicon-gallery tw-text-4xl tw-text-white"></i></div>`;
        html += `
        <a href="${list[key].link}"  class="hotel-item tw-rounded-md tw-border tw-border-solid tw-border-gray-300 tw-flex tw-text-gray-700 tw-overflow-hidden hover:tw-shadow-lg tw-transition-all">
          ${img}
          <div class="tw-p-4 tw-flex tw-flex-col tw-gap-2 tw-grow">
            <h3 class="tw-font-bold tw-text-lg tw-line-clamp-1 tw-text-black">${list[key].name}</h3>
            <div class="tw-font-bold">Location: <span class="tw-font-normal">${list[key].city}</span></div>
            <div class="tw-font-bold">Price: <span class="tw-font-normal">${list[key].priceRange.min}-${list[key].priceRange.max}</span></div>
            <div class="tw-font-bold">Rating: <span class="tw-font-normal">${list[key].rate}</span></div>
          </div>
        </a>
        `;
      }
      return html;
    }

    generateLoading(selector, number = 6) {
      let skeleton = "";
      for (let i = 0; i < number; i++) {
        skeleton += `
        <div  class="tw-rounded-xl tw-border tw-border-solid tw-border-gray-300 tw-flex tw-text-gray-700  tw-overflow-hidden">
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
  }
  const publics = new Publics();
})(jQuery);
