import _ from 'underscore';
import Backbone from 'backbone'; 
(function($) {
	var AggregateRating = Backbone.Model.extend({
		idAttribute: "id",
		url: ajaxurl+'?action=aggregate_rating',
		defaults: {
			post_id: null,
			value: null
		},
	});

	var Rating = Backbone.Model.extend({
		idAttribute: "id",
		url: ajaxurl+'?action=rating',
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

	$('.input-add-rating').on('change', function() {
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
			that.parent('div').replaceWith("<small class=\"alert alert-success\">Your rating have been submitted</small>");
			new AggregateRating.DetailView();
		}, 1000);
		
	});
	$('.input-add-rating').on('input', function() {
		var $that = $(this);
		$(this).next().html($that.val() + "/10");
	});

	new AggregateRating.DetailView();

}(jQuery));