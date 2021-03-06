requirejs.config({
	baseUrl: './public/js/self',
	paths: {
		wind: "../wind",
		frontend: "../frontend",
		jquery: "../jquery",
		bootstrap: "./lib/bootstrap.min",
		ueditorconfig: "../ueditor/ueditor.config",
		ueditorall: "../ueditor/ueditor.all.min",
		common:"./_common",
		face: "face"
	}
});
require(['jquery', 'wind'], function($) {
	require(['frontend', 'bootstrap', 'crop_image', 'upload','common'], function(frontend, bs, ci, upload,comm) {

		//hash
		if (window.location.hash == "#upload_cover") {
			$("#upload-cover").show();
			$("#uploadBtn").addClass("focus");
			$("#edit-msg").removeClass('focus');
			$("#crt-group-form").hide();
		}
		//活动封面上传
		function act_cover_upload() {
			var croppedCanvas;
			var roundedCanvas;
			// Crop
			croppedCanvas = $("#cropper-target").cropper('getCroppedCanvas');
			// Round
			roundedCanvas = ci.g(croppedCanvas);

			$.ajax({
				type: "POST",
				url: "./index.php?g=Portal&m=activity&a=upload_cover&id="+comm.getQueryString("id"),
				contentType: "multipart/form-data",
				data: croppedCanvas.toDataURL(), //将canvas保存并上传
				dataType: 'json',
				success: function(result, status, xhr) {
						noty({
						text: result.info,
						type: 'success',
						layout: 'center'
					});
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					alert("上传失败，请检查网络后重试");
				}
			});
		}
		upload.init("#act-upload", act_cover_upload);

		
		
		var parentTime = new Date().getTime(); //当前时间的时间戳

		var startT = $("input[name=start_time]");
		/*开始时间不能小于目前时间*/
		startT.blur(function() {
			var ths = $(this),
				startVal = ths.val(),
				startDate = new Date(startVal),
				startTime = startDate.getTime(); //开始时间的时间戳

			if (startTime < parentTime) {
				noty({
					text: "不能设置过去的时间为开始活动时间",
					type: 'error',
					layout: 'center'
				});
				ths.val(""); //清空所设置的时间
			}
		});
		/*判断结束时间是否合法*/
		$("input[name=end_time]").blur(function() {
			var ths = $(this),
				startVal = startT.val();
			//当开始时间为空时
			if (startVal == "") {
				noty({
					text: "请先设置开始时间",
					type: 'error',
					layout: 'center'
				});
				ths.val("");
			}

			var endVal = ths.val(),
				endDate = new Date(endVal),
				endTime = endDate.getTime(),
				startTime = new Date(startVal).getTime();

			if (endTime < startTime) { //结束时间在开始时间之前
				noty({
					text: "结束时间不能在开始时间之前！",
					type: 'error',
					layout: 'center'
				});
				ths.val("");
			}
		})

	})
});