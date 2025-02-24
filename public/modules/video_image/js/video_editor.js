
$(document).ready(function () {
    let videoSegmentIndex = 1;
    $('#addVideoSegmentBtn').on('click', function () {
        const rowHtml = `
            <div class="row mb-2 video-segment-row">
                <div class="col-md-3">
                    <input type="file" name="videos[]" class="form-control" accept="video/*" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="segments[${videoSegmentIndex}][start]" class="form-control" placeholder="Start (giây)" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="segments[${videoSegmentIndex}][end]" class="form-control" placeholder="End (giây)" required>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-danger btn-sm removeSegmentBtn">Xóa</button>
                </div>
            </div>
        `;
        $('#videoSegmentsContainer').append(rowHtml);
        videoSegmentIndex++;
    });

    // Sử dụng event delegation để xóa row khi click vào nút removeSegmentBtn
    $(document).on('click', '.removeSegmentBtn', function () {
        $(this).closest('.video-segment-row').remove();
    });


    // Các đoạn mã khác...


    $('#keepSegmentsAudio').change(function () {
        if (!this.checked) {
            $('#audioSegmentsDiv').slideDown();
        } else {
            $('#audioSegmentsDiv').slideUp();
        }
    });

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).data('bsTarget');
        if (target === "#overlay-form") {
            $('#bottomPart').show();
            __setTimelineHeight(); // Tính lại chiều cao timeline khi hiển thị
        } else {
            $('#bottomPart').hide();
        }
    });
    const cursorWidth = $(".cursor").width();
    const rulerWidth = $(".ruler").width();
    const rulerWrapperOffset = parseInt($(".ruler-wrapper").css("padding-left").replace('px', ''));

    var cursorIsDragged = false;
    var videoDuration = 0;
    $('#createVideoBtn').click(function (e) {
        e.preventDefault();
        showLoading();
        var form = $('#createVideoForm')[0];
        var formData = new FormData(form);

        $.ajax({
            url: '/create-basic-video',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();
                // Cập nhật nguồn video demo với previewUrl nhận được từ backend
                $('#video source').attr('src', response.previewUrl);
                $('#video')[0].load();

                // Tạo nút "Xem demo" nếu chưa có
                if ($('#videoPreviewBtn').length === 0) {
                    $('#video').closest('.col-3').append(`
                        <div id="videoPreviewContainer" class="mt-2">
                            <button type="button" id="videoPreviewBtn" class="btn btn-info">Xem demo</button>
                        </div>
                    `);
                }

                // Gán sự kiện cho nút "Xem demo" để mở modal preview
                $('#videoPreviewBtn').off('click').on('click', function () {
                    $('#previewModal').modal('show');
                });
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire('Lỗi!', 'Có lỗi xảy ra: ' + xhr.responseJSON.message, 'error');
            }
        });
    });

    // Hiển thị/ẩn phần chọn file audio khi không giữ audio gốc
    $('#keepVideoAudio').change(function () {
        if (!this.checked) {
            $('#audioConcatDiv').slideDown();
        } else {
            $('#audioConcatDiv').slideUp();
        }
    });

    // Hiển thị/ẩn phần tùy chọn chuyển cảnh
    $('#applyTransition').change(function () {
        if (this.checked) {
            $('#transitionOptions').slideDown();
            $('#transitionOptions input').attr('required', true);
        } else {
            $('#transitionOptions').slideUp();
            $('#transitionOptions input').removeAttr('required');
        }
    });

    $('#concatVideosBtn').click(function (e) {
        e.preventDefault();
        showLoading();
        var form = $('#concatVideosForm')[0];
        var formData = new FormData(form);

        $.ajax({
            url: '/create-video-with-audio',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();

                // Nếu backend trả về previewUrl thì cập nhật video demo
                if (response.previewUrl) {
                    $('#video source').attr('src', response.previewUrl);
                    $('#video')[0].load();

                    // Tạo nút "Xem demo" nếu chưa có
                    if ($('#videoPreviewBtn').length === 0) {
                        $('#video').closest('.col-3').append(`
                            <div id="videoPreviewContainer" class="mt-2">
                                <button type="button" id="videoPreviewBtn" class="btn btn-info">Xem demo</button>
                            </div>
                        `);
                    }

                    // Gán sự kiện cho nút "Xem demo" để mở modal preview
                    $('#videoPreviewBtn').off('click').on('click', function () {
                        $('#previewModal').modal('show');
                    });
                }

                Swal.fire("Thành công!", response.message, "success");
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire("Lỗi!", 'Có lỗi xảy ra: ' + (xhr.responseJSON.message || 'Vui lòng kiểm tra lại.'), "error");
            }
        });
    });

    $('#extractAudioForm').submit(function (e) {
        e.preventDefault();
        showLoading(); // Hàm hiển thị loading (nếu đã định nghĩa)
        var form = $('#extractAudioForm')[0];
        var formData = new FormData(form);

        $.ajax({
            url: '/extract-audio',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading(); // Hàm ẩn loading
                Swal.fire("Thành công!", response.message, "success");
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire("Lỗi!", 'Có lỗi xảy ra: ' + (xhr.responseJSON.message || 'Vui lòng kiểm tra lại.'), "error");
            }
        });
    });


    $(document).ready(function () {

        __setTimelineHeight();
        $(window).on("resize", function () {
            __setTimelineHeight();
        });

        __prepareDragCursor();

        $("video").on(
            "timeupdate",
            function (event) {
                moveCursorToSecond(this.currentTime);
            }
        );

        setTimeout(() => {
            videoDuration = $("video").get(0).duration;

            // Add seconds in the ruler
            __addUnits();

        }, 100);

        setTimeout(() => {
            __loadItems();
        }, 200);
    });

    function __loadItems() {
        // TODO: Call ajax to get all items
        // If success:
        __addItem("item_0", "00:00:01", "00:00:10", 10, 100, "redBg", "Hello World!");
    }

    function __setTimelineHeight() {
        $("#bottomPart").height($("body").height() - $("#topPart").height());
        $(".timeline").height($("#timeline-editor").height());
        $(".cursor-timeline").height($("#timeline-editor").height() -
            $(".sticky-top").height()
        );
    }

    /******************* HELPER function for conversions ***********************/

    function treeDec(value) {
        return ((value < 100) ? ((value < 10) ? ("00") : ("0")) : ("")) + value;
    }

    function twoDec(value) {
        return ((value < 10) ? ("0") : ("")) + value;
    }

    /**
     * Converts as hh:mm:ss.sss a timer in second
     * 
     * @param {integer} seconds 
     */
    function convertSecondsToHms(seconds) {
        var nbMitutes = Math.trunc(seconds / 60);
        var nbHours = Math.trunc(nbMitutes / 60);
        var nbSeconds = Math.trunc(seconds - (nbMitutes * 60));
        nbMitutes -= nbHours * 60;
        var millisec = Math.trunc((seconds - nbSeconds) * 1000);
        return twoDec(nbHours) + ":" + twoDec(nbMitutes) + ":" + twoDec(nbSeconds) + "." + treeDec(millisec);
    }

    /**
     * Converts as second a timer in hh:mm:ss.sss
     * 
     * @param {integer} seconds 
     */
    function convertHmsToSeconds(hmsString) {
        if (hmsString.indexOf(":") == -1) {
            console.error("No ':' in hmsString...");
            return NaN;
        }
        const data = hmsString.split(":");
        if (data.length !== 3) {
            console.error("Not enought items in " + data);
            return NaN;
        }
        const h = parseInt(data[0]);
        if (h == NaN) {
            console.error("Conversion to integer failed with " + data[0]);
            return NaN;
        }

        const m = h * 60 + parseInt(data[1]);
        if (m == NaN) {
            console.error("Conversion to integer failed with " + data[1]);
            return NaN;
        }
        const value = m * 60 + parseFloat(data[2]);
        if (value == NaN) {
            console.error("Conversion to float failed with " + data[2]);
        }
        return value;
    }

    /**
     * Converts as pixel a timer in second
     * 
     * @param {integer} seconds 
     */
    function timerSecondsToPixel(currentTime) {
        return rulerWidth * (currentTime / videoDuration);
    }

    /**
     * This function is triggered by the video player, when:
     *  1. the video plays
     *  2. the currentTime attribute is set on the video object
     * 
     * @param {number} timerSeconds 
     * @returns 
     */
    function moveCursorToSecond(timerSeconds) {
        // Update timers
        $(".currentValue").html(convertSecondsToHms(timerSeconds));

        // Move current time vertical bar
        const ratio = timerSeconds / videoDuration;
        const leftOffsetForTimeline = rulerWrapperOffset + (ratio * rulerWidth) - 1;
        $(".cursor-timeline").css({
            left: leftOffsetForTimeline + "px"
        });

        // Move cursor
        if (cursorIsDragged) {
            return;
        }
        const leftOffsetForCursor = (ratio * rulerWidth) - (cursorWidth / 2) + 2;
        $(".cursor").css({
            left: leftOffsetForCursor + "px"
        });

    }

    /*************** HELPER function to move the video and the cursor **************/

    /**
     * Move the cursor and the video at a timer, in hh:mm:ss.sss
     * 
     * @param {string} timerHms 
     */
    function moveToHms(timerHms) {
        // This will call the update of the cursor
        moveToSecond(convertHmsToSeconds(timerHms));
    }

    /**
     * Move the cursor and the video at a timer, in percent of the video
     * 
     * @param {number} ratio (value in [0 ... 1])
     */
    function moveToPercent(ratio) {
        // This will call the update of the cursor
        moveToSecond(ratio * videoDuration);
    }

    /**
     * Move the cursor and the video at a timer, in second
     * 
     * @param {number} timerSecond 
     */
    function moveToSecond(timerSecond) {
        if (!timerSecond) {
            return;
        }
        // This will call the update of the cursor
        $("video").get(0).currentTime = Math.round(timerSecond * 100) / 100;
    }


    /**
     * Make the cursor draggable on the X-Axis
     */
    function __prepareDragCursor() {
        // Get current offset of ruler
        const leftBorder = -cursorWidth / 2 + 2;
        const rightBorder = rulerWidth - cursorWidth / 2;
        $(".cursor").draggable({
            axis: "x",
            cursor: "move",
            drag: function (event, ui) {
                cursorIsDragged = true;

                var leftPosition = ui.position.left;
                if (leftPosition < leftBorder) {
                    ui.position.left = leftBorder;
                } else if (leftPosition > rightBorder) {
                    ui.position.left = rightBorder;
                }
                $('video').trigger('pause');

                ratio = (leftPosition - leftBorder) / rulerWidth;
                moveToPercent(ratio);

            },
            stop: function (event, ui) {
                cursorIsDragged = false;
            }
        });
    }

    function __addItem(itemId, startTime, endTime, pos_x, pos_y, css_class, text) {
        var template = '<div class="h-line dropdown">' +
            '<div class="item" id="' + itemId + '" data-start="' + startTime + '" data-end="' + endTime + '" ' +
            'data-pos_x="' + pos_x + '" data-pos_y="' + pos_y + '" data-css_class="' + css_class + '" title="' + text + '">' +
            '<div class="title">' + text + '</div>' +
            '<div class="dropdown-content btn-group">' +
            '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" ' +
            'data-bs-auto-close="true" aria-expanded="false">Actions</button>' +
            '<ul class="dropdown-menu">' +
            '<li><h6 class="dropdown-header">Modifier ...</h6></li>' +
            '<li><span class="dropdown-item" onclick="setStartTimeWithCursor()">le début sur le curseur</span></li>' +
            '<li><span class="dropdown-item" onclick="setEndTimeWithCursor()">la fin sur curseur</span></li>' +
            '<li><hr class="dropdown-divider"></li>' +
            '<li><h6 class="dropdown-header">Afficher ...</h6></li>' +
            '<li><span class="dropdown-item" onclick="moveToStartOf(\'' + itemId + '\')">Le début</span></li>' +
            '<li><span class="dropdown-item" onclick="moveToEndOf(\'' + itemId + '\')">La fin</span></li>' +
            '</ul>' +
            '</div>' +
            '</div>' +
            '</div>';

        $(".timeline").append(template);

        // Tính toán số pixel từ thời gian
        var startSecond = convertHmsToSeconds(startTime);
        var endSecond = convertHmsToSeconds(endTime);
        moveItem(itemId, startSecond, endSecond, false);

        var leftBorder = 0;
        var rightBorder = rulerWidth - 3;

        $("#" + itemId).draggable({
            axis: "x",
            cursor: "move",
            drag: function (event, ui) {
                selectItem(itemId);
                var leftPosition = ui.position.left;
                var rightPotition = ui.position.left + ui.helper.width();
                if (leftPosition < leftBorder) {
                    ui.position.left = leftBorder;
                } else if (rightPotition > rightBorder) {
                    ui.position.left = rightBorder - ui.helper.width();
                }
                var startTimeSecond = (ui.position.left / rulerWidth) * videoDuration;
                var endTimeSecond = ((ui.position.left + ui.helper.width()) / rulerWidth) * videoDuration;
                __afterMoveItem(itemId, startTimeSecond, endTimeSecond, true);
            }
        });

        $("#" + itemId).on("click", function (event) {
            selectItem(this.id);
        });
    }

    function selectItem(itemId) {
        if (itemId == null) {
            $("#formEditItem").hide();
            return;
        }

        const jqItem = $("#" + itemId);
        if (jqItem.hasClass("selected")) {
            // Do not select the selected item
            return;
        }

        // Unselect the selectedItem and check if updates are not saved
        if ($("#formEditItem").is(":visible") && $("#alertUpdate").is(":visible") && $("#formEditItem #itemId").val() != itemId) {
            if (!confirm("Des modifications n'ont pas été sauvegardées. Annuler pour appliquer les changements.")) {
                return;
            } else {
                // Discard changes: move the previous item where it should be.
                cancelUpdates(true);
            }
        }

        // Select the item in the timeline
        $(".item").removeClass("selected");
        jqItem.addClass("selected");

        // Fill the form with values
        $("#formEditItem #itemId").val(jqItem.get(0).id);
        $("#formEditItem #startTime").val(jqItem.data("start"));
        $("#formEditItem #endTime").val(jqItem.data("end"));
        $("#formEditItem #pos_x").val(jqItem.data("pos_x"));
        $("#formEditItem #pos_y").val(jqItem.data("pos_y"));
        $("#formEditItem #css_class").val(jqItem.data("css_class"));
        $("#formEditItem #text").val(jqItem.attr("title"));
        markItemChanged(false);

        // Show the form
        $("#formEditItem").show();
    }

    function __addUnits() {
        // Add the 0
        const template = '<span class="unit" style="left: 0px"></span>';
        $(".ruler").append(template);

        for (let i = 1; i < videoDuration; i++) {
            const leftPosition = timerSecondsToPixel(i);
            const template = '<span class="unit" style="left: ${leftPosition}px">${i}</span>';
            $(".ruler").append(template);
        }
    }

    function moveToStartOf(itemId) {
        moveToHms($("#" + itemId).data("start"));
    }

    function moveToEndOf(itemId) {
        moveToHms($("#" + itemId).data("end"));
    }

    function setStartTimeWithCursor() {
        const startTimeSecond = $("video").get(0).currentTime;
        const startTimeHms = convertSecondsToHms(startTimeSecond);
        $("#formEditItem #startTime").val(startTimeHms);
        const itemId = $("#formEditItem #itemId").val();

        // Apply modification in the item of the timeline
        const endTimeSecond = convertHmsToSeconds($("#formEditItem #endTime").val());
        moveItem(itemId, startTimeSecond, endTimeSecond);
    }

    function setEndTimeWithCursor() {
        const endTimeSecond = $("video").get(0).currentTime;
        const endTimeHms = convertSecondsToHms(endTimeSecond);
        $("#formEditItem #endTime").val(endTimeHms);
        const itemId = $("#formEditItem #itemId").val();

        const startTimeSecond = convertHmsToSeconds($("#formEditItem #startTime").val());

        if (endTimeSecond <= startTimeSecond) {
            endTimeSecond = startTimeSecond + 1;
        }
        if (startTimeSecond !== NaN && endTimeSecond !== NaN) {
            moveItem(itemId, startTimeSecond, endTimeSecond);
        }
        markItemChanged(true);

    }

    function updateTimeline() {
        const itemId = $("#formEditItem #itemId").val();
        const startTimeSecond = convertHmsToSeconds($("#formEditItem #startTime").val());
        const endTimeSecond = convertHmsToSeconds($("#formEditItem #endTime").val());
        if (endTimeSecond <= startTimeSecond) {
            endTimeSecond = startTimeSecond + 1;
        }
        if (startTimeSecond !== NaN && endTimeSecond !== NaN) {
            moveItem(itemId, startTimeSecond, endTimeSecond);
        }
        markItemChanged(true);
    }

    function moveItem(itemId, startTimeSecond, endTimeSecond, changed) {
        const itemLeft = timerSecondsToPixel(startTimeSecond);
        const itemWidth = timerSecondsToPixel(endTimeSecond) - itemLeft;

        $("#" + itemId).css({
            width: itemWidth,
            left: itemLeft
        });

        __afterMoveItem(itemId, startTimeSecond, endTimeSecond, changed);

    }

    function __afterMoveItem(itemId, startTimeSecond, endTimeSecond, changed) {

        const startTimeHms = convertSecondsToHms(startTimeSecond);
        const endTimeHms = convertSecondsToHms(endTimeSecond);

        // Check that the popup menu still visible
        const itemLeft = $("#" + itemId).position().left;
        const itemWidth = $("#" + itemId).width();
        if (itemLeft + itemWidth > (rulerWidth * 0.5)) {
            $("#" + itemId + " .dropdown-content").css({
                left: -70
            });
        } else {
            $("#" + itemId + " .dropdown-content").css({
                left: itemWidth + 6
            });
        }

        // Update the form if it's visible, for the current item
        if ($("#formEditItem").is(":visible") && $("#itemId").val() == itemId) {
            $("#formEditItem #startTime").val(startTimeHms);
            $("#formEditItem #endTime").val(endTimeHms);

            if (changed === undefined) {
                markItemChanged(true);
            } else {
                markItemChanged(changed);
            }
        }

    }

    function playItem() {
        const startTimeSecond = convertHmsToSeconds($("#formEditItem #startTime").val());
        moveToSecond(startTimeSecond);
        $("video").get(0).play();
    }

    function cancelUpdates(wasWarned) {
        if (wasWarned === false) {
            if ($("#alertUpdate").is(":visible")) {
                if (!confirm("Voulez-vous abandonner vos modifications ?")) {
                    return;
                }
            }
        }

        // Get the current timers
        const itemId = $("#formEditItem #itemId").val();
        const startTimeSecond = convertHmsToSeconds($("#" + itemId).data("start"));
        const endTimeSecond = convertHmsToSeconds($("#" + itemId).data("end"));
        moveItem(itemId, startTimeSecond, endTimeSecond, false);

        $("#formEditItem").hide();
        $("#formEditItem #itemId").val("");
        $(".item").removeClass("selected");

    }

    function addItem() {
        const startTimeSecond = $("video").get(0).currentTime;
        const startTimeHms = convertSecondsToHms(startTimeSecond);
        const nbItems = $(".item").length;
        __addItem("item_" + nbItems, startTimeHms, convertSecondsToHms(startTimeSecond + 1), 0, 0, "", "sous-titre");
        selectItem("item_" + nbItems);

    }

    function markItemChanged(hasChanged) {
        if (hasChanged) {
            // Show the red bullet
            $("#alertUpdate").show();
        } else {
            // Hide the red bullet
            $("#alertUpdate").hide();
        }
    }

    function applyUpdates() {

        // TODO: Call Ajax to save in database
        // If success:

        const itemId = $("#formEditItem #itemId").val();

        $("#" + itemId).data("start", $("#formEditItem #startTime").val());
        $("#" + itemId).data("end", $("#formEditItem #endTime").val());
        $("#" + itemId).data("pos_x", $("#formEditItem #pos_x").val());
        $("#" + itemId).data("pos_y", $("#formEditItem #pos_y").val());
        $("#" + itemId).data("css_class", $("#formEditItem #css_class").val());
        $("#" + itemId).attr("title", $("#formEditItem #text").val());
        $("#" + itemId + " .title").html($("#formEditItem #text").val());
        markItemChanged(false);
    }

    $('#editor').show();
    $('#editorTabs a#basic-video-tab').tab('show');

    function showLoading() {
        console.log("Hiển thị loading...");
        if ($('#loadingOverlay').length === 0) {
            $('body').append(`
                    <div id="loadingOverlay">
                        <div class="loader"></div>
                    </div>
                `);
        }
    }

    function hideLoading() {
        console.log("Ẩn loading...");
        $('#loadingOverlay').remove();
    }

    $('#concatVideoSegmentsForm').submit(function (e) {
        e.preventDefault();
        showLoading();
        var form = $(this)[0];
        var formData = new FormData(form);
        $.ajax({
            url: '/concat-video-segments-preview',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                hideLoading();
                // Cập nhật video demo
                $('#video source').attr('src', response.previewUrl);
                $('#video')[0].load();

                // Tạo nút "Xem demo" nếu chưa có
                if ($('#videoPreviewBtn').length === 0) {
                    $('#video').closest('.col-3').append(`
                        <div id="videoPreviewContainer" class="mt-2">
                            <button type="button" id="videoPreviewBtn" class="btn btn-info">Xem demo</button>
                        </div>
                    `);
                }

                // Gán sự kiện cho nút "Xem demo" để mở modal preview
                $('#videoPreviewBtn').off('click').on('click', function () {
                    // Mở modal đã được định nghĩa trong Blade (xem bước 2)
                    $('#previewModal').modal('show');
                });
            },
            error: function (xhr) {
                hideLoading();
                Swal.fire('Lỗi!', 'Có lỗi xảy ra: ' + xhr.responseJSON.message, 'error');
            }
        });
    });

    $('#previewModal').on('show.bs.modal', function () {
        var demoUrl = $('#video source').attr('src');
        $('#previewVideo source').attr('src', demoUrl);
        $('#previewVideo')[0].load();
    });

    $('#exportFileBtn').click(function () {
        $.ajax({
            url: '/confirm-export',
            type: 'POST',
            data: { outputFile: $('#outputSegmentsFile').val(), _token: $('input[name=_token]').val() },
            success: function (resp) {
                // Tạm dừng video demo để âm thanh dừng ngay
                $('#previewVideo')[0].pause();
                // Ẩn modal sau khi dừng video
                $('#previewModal').modal('hide');
                Swal.fire('Thành công!', resp.message, 'success');
            },
            error: function (xhr) {
                Swal.fire('Lỗi!', 'Có lỗi xảy ra khi xuất file', 'error');
            }
        });
    });
});

