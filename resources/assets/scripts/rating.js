import _ from 'underscore';
import Backbone from 'backbone'; 
(function($) {
	var AggregateRating = Backbone.Model.extend({
		idAttribute: "id",
		url: ajaxurl+'?action=aggregate_optirating',
		defaults: {
			post_id: null,
			value: null
		},
	});

	var Rating = Backbone.Model.extend({
		idAttribute: "id",
		url: ajaxurl+'?action=optirating',
		defaults: {
			post_id: null,
			value: null
		},
	});

	AggregateRating.DetailView = Backbone.View.extend({
		model: new AggregateRating(),
		tagName: 'div',
	    // Get the template from the DOM
	    initialize: function() {
	    	this.template = _.template( $('#arating-detail-template').html() );
	    	var post_id = $('#arating-detail-template').data("postId");
	    	this.setElement($(".arating-detail-" + post_id)),
			this.model.on('change', this.render, this);
			this.model.fetch({
				beforeSend: function(xhr) {
	    			xhr.setRequestHeader('postid', post_id);
	    		}
			});
	    },
	    render: function() {
	    	this.$el.html(this.template(this.model.toJSON()));
	    	return this;
	    }
	});

	$('.games-rating-stars input:radio').on('change', function() {
		var rated = $.cookie('alreadyRated' + $(this).data('postId'));
		if (rated == 1) {
			$(this).parent('form').parent('div').replaceWith("<div class=\"alert alert-warning\">Your have already rated this game.</div>");
		}
		if ($(this).val() === '') {
			return false;
		}
		var that = $(this);
		var r = new Rating({
			post_id: that.data('postId'),
			value: that.val()
		});
		r.save();
		setTimeout(function() {
			that.parent('form').parent('div').replaceWith("<div class=\"alert alert-success\">Your rating have been submitted</div>");
			new AggregateRating.DetailView();
		}, 1500);
		that.parent().next('.rating-preview').html(that.val() + "/10");
		$.cookie('alreadyRated' + that.data('postId'), '1', { expires: 31 });
	});

	new AggregateRating.DetailView();

}(jQuery));