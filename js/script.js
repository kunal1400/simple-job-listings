jQuery(document).ready(function() {

	jQuery(".requestLeadDetails").on("click", function() {
		var selectedNode = jQuery(this)
		selectedNode.html("Requesting...")
		var postId = jQuery(this).attr("data-postId")
		var userId = jQuery(this).attr("data-userId")
		jQuery.ajax({
			url: simple_jobs_js_var.ajax_url,
			method: 'POST',
			data: {
				action: 'request_lead_details',
				postId,
				userId
			},
			success: function(response) {
				selectedNode.html("Requested")
				console.log(response, 'success')
			},
			error: function(error) {				
				console.log(error, 'error')
			}
		})
	})

	jQuery(".adminApproveViewLeadBtn").on("click", function() {
		var selectedNode = jQuery(this)
		selectedNode.html("Approving...")
		var postId = jQuery(this).attr("data-postId")
		var userId = jQuery(this).attr("data-userId")
		jQuery.ajax({
			url: simple_jobs_js_var.ajax_url,
			method: 'POST',
			data: {
				action: 'action_request_lead_details',
				string: 'approve',
				postId,
				userId
			},
			success: function(response) {
				selectedNode.html("Approved")
				console.log(response, 'success')
			},
			error: function(error) {				
				console.log(error, 'error')
			}
		})
	})

	jQuery(".adminDeclineViewLeadBtn").on("click", function() {
		var selectedNode = jQuery(this)
		selectedNode.html("Rejecting...")
		var postId = jQuery(this).attr("data-postId")
		var userId = jQuery(this).attr("data-userId")
		jQuery.ajax({
			url: simple_jobs_js_var.ajax_url,
			method: 'POST',
			data: {
				action: 'action_request_lead_details',
				string: 'decline',
				postId,
				userId
			},
			success: function(response) {
				selectedNode.html("Rejected")
				console.log(response, 'success')
			},
			error: function(error) {				
				console.log(error, 'error')
			}
		})
	})

})