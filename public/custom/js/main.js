/* 
 * custom/main.js
 */


/*
 * 
 * questionPop interface:
 * 
 * $(element).quesitonPop(options);
 * 
 * @param      char $
 * @returns    {JQuery.Node}
 */
(function($) {
    
    /**
     * Constructor function for yes/no dialog popup.
     * 
     * Note dependencies: Swal, jQuery
     * 
     * @param Object options {
     *            string title
     *            string htmlQuestion
     *            string yestText
     *            string noText
     *            string type
     *            string ajaxUrl
     *        }
     */
    function QuestionPop(options, $t) {

        let settings = {};
        $.extend(true, settings, options, $t.data());
        this.settings = settings;

        // standard Javascript 'hack' for scope with 'this.that'
        this.that = {};
        this.that.settings = this.settings;
        this.that.$t = $t;
        
        this.fire = () => {

            Swal.fire({
                title: this.settings['title'],
                type: this.settings['type'],
                html: this.getHtmlQuestion(),
                showCancelButton: true,
                cancelButtonClass: 'btn btn-light',
                cancelButtonText: this.settings['noText'],
                confirmButtonClass: 'btn btn-danger',
                confirmButtonText: this.settings['yesText']
            }).then((result) => {

                if (!result.value) {
                    console.log("QuestionPop: Swal.fire has no 'result' value.");
                    return false;
                }

                if(this.settings['ajaxUrl']) {
                    $.ajax({
                        'url': this.that.settings['ajaxUrl'],
                        'type': 'post',
                    }).done((response) => {
                        var message = response['message'] ? response['message'] : 'OK';

                        this.that.$t.trigger('success.qp');

                        if(this.that.settings['showSystemMessage']) {
                            showSystemMessage(message);
                        }
                    });
                }
                else {
                    this.that.$t.trigger('success.qp');
                }

            });
        }
    }
    
    /*
     * Construct HTML question
     * 
     * Return 'htmlQuestion' if it's set in settings
     * Return string constructed from 'text' and 'label' if no 'htmlQuestion' is
     * specified
     * 
     * @return string (HTML)
     */
    QuestionPop.prototype.getHtmlQuestion = function() {
        
        let boldSpecifier = "#~b~#";
        
        if (this.settings['htmlQuestion']) {
            return this.settings['htmlQuestion'];
        }
        else if(this.settings['text']) {
            
            let boldText = '';
            if(this.settings['label']) {
                boldText = '<b>' + this.settings['label'] + '</b>';
            }
            
            if (this.settings['text'].indexOf(boldSpecifier) !== -1) {
                return this.settings['text'].replace(boldSpecifier, boldText);
            }
            
            return this.settings['text'] + boldText;
        }
        return 'Are you sure you wish to perform this action?';
    };
    
    
	/**
     *  @param   object params
     *  @returns jquery.node
     */
	$.fn.questionPop = function(options) {

		// merge default and user parameters
        var parameters = $.extend(true, {}, $.fn.questionPop.defaults, options);
        
        if(parameters['liveSelector'] != undefined) {
            this.on('click', parameters.liveSelector, function() {
                var $t = $(this);
                $.fn.questionPop.instantiate(parameters, $t);
            });
        }
        else {
            this.on('click', function() {
                var $t = $(this);
                $.fn.questionPop.instantiate(parameters, $t);
            });
        }

		// allow jQuery chaining
		return this;
	};
	
	$.fn.questionPop.defaults = {
        'title': 'Perform action',
        'yesText': 'Yes',
        'noText': 'No',
        'type': 'warning',
        'callAjax': false,
        'showSystemMessage': true
    };
    
    $.fn.questionPop.instantiate = (parameters, $t) => {
        let qp = new QuestionPop(parameters, $t);
        return qp.fire();
    };

})(jQuery);



/**
 *  Plugin for the tags selec2 and their crud
 * 
 *  @param      char $
 *  @returns    {JQuery.Node}
 */
(function($) {
	/**
     *  @param   object params
     *  @returns jquery.node
     */
	$.fn.tagsSelect = function(options) {
		// merge default and user parameters
        var parameters = $.extend(true, {}, $.fn.tagsSelect.defaults, options);
		
		var select2Config = {
			multiple: parameters.multiple,
			maximumSelectionLength: parameters.maximumSelectionLength,
			placeholder: parameters.placeholder,
			width: '100%',
			ajax: {
				url: parameters.sourceUrl,
				dataType: 'json',
				type: 'POST',
				delay: 1000
			}
		};

		if(parameters['dropdownParent']) {
			select2Config.dropdownParent = parameters['dropdownParent'];
		}
		
		if (parameters.enableCreate) {
			select2Config.tags = true;
			
			select2Config.createTag = function (params) {
				var term = $.trim(params.term);
                                
				if (term === '') {
				  return null;
				}

				return {
				  id:       0,
				  text:     term + parameters.newTagLabel,
				  textOriginal: term,
				  newTag:   true
				};
			};
			
			select2Config.templateSelection = function (state) {
				if (!state['newTag']) {
					return state.text;
				}
				
				return $(`<span class="text-danger"> ${state.text}</span>`);
			};
		}
		// traverse all nodes
		this.each(function() {

			// express a single node as a jQuery object
			var $t = $(this);
            
            // mambo-jambo ...
            $t.select2(select2Config);
			
			if (!parameters.enableCreate) {
				//enableCreate is false just finish configuring
				return;
			}
			
			//enableCreate is true
			$t.on('select2:select', function (e) {
				//e.stopPropagation();
				//e.preventDefault();

				var tag = e.params.data;

				if (!tag['newTag']) {
					//selected tag is not new tag just return
					return;
				}
				var data = $t.select2('data');
			
				for(var index in data) {
					if (data[index]['newTag']) {
						data.splice(index, 1);
						continue;
					}
				}

                                // clean up select2 options 
				$.fn.tagsSelect.setSelect2Options($t, data, true);
				
				//new tag was selected call handleStoring and show form
				$.fn.tagsSelect.handleStoringTag(0, tag['textOriginal'], function(newTag) {
					//remove selected "(NEW)" tag
					var data = $t.select2('data');
					data.push(newTag);
					
					$.fn.tagsSelect.setSelect2Options($t, data, true);
					
				}, parameters);
			});
		});

		// allow jQuery chaining
		return this;
	};
	
	$.fn.tagsSelect.defaults = {
            multiple: true,
                    maximumSelectionLength: 0,
                    placeholder: null,
            sourceUrl: '/tags/populate-search', 
                    storeUrl: '/tags/store',
                    enableCreate: true,
                    newTagLabel: ' (NEW TAG)',
                    translations: {
                cancel: 'No, Cancel!',
                submit: 'Submit',
                tags_form_title: 'Tag Settings',
                successful_save: 'Tag successfully saved',
                error_title: 'Ooops..',
                error_text: 'Looks like something went wrong'
            },
                    csrfToken: $('meta[name="csrf-token"]').attr('content'),
                    noSwal: false,
                    showSystemMessageOnCreate: true
        };
	
	$.fn.tagsSelect.setSelect2Options = function($t, data, triggerChange) {
		$t.empty();
		var selectedTagIds = [];
                
		for(var index in data) {
			
			$t.append(new Option(data[index]['text'], data[index]['id'], false, false));
			selectedTagIds.push(data[index]['id']);
		}

		$t.select2('data', data);
		$t.val(selectedTagIds);

		// trigger change just in case ;)
		if (triggerChange) {
			$t.trigger('change');
		}
	};
	
	$.fn.tagsSelect.handleStoringTag = function (tagId, tagText, successHandler, options) {
		var parameters = $.extend(
			true,
            {}, 
            $.fn.tagsSelect.defaults, 
            options
		);
		
		let ajaxCall = function(title) {
			// do ajax submit of tags form
			$.ajax({
				url: parameters.storeUrl,
				method: 'post',
				data: {
					title: title
				},
				success: function(data) {
					if(parameters['showSystemMessageOnCreate']) {
						showSystemMessage(data['message']);
					}

					if($('#datatables').length > 0) {
						$('#datatables').DataTable().draw();
					} else {
						console.log('No datatables to reload');
					}

					if (typeof successHandler == 'function') {
						successHandler({
							'id': data['data']['id'],
							'text': data['data']['title']
						});
					}
				},
			});
		}
                
                ajaxCall(tagText);
	};
})(jQuery);