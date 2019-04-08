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



