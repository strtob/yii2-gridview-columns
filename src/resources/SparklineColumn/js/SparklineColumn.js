$(document).ready(function() {

    $('.sparkline').each(function() {
        var $this = $(this);
        var values = $this.data('values');
        var options = $this.data('options');
        
        // Use the sparkline library to generate the sparkline
        $this.sparkline(values, options);
    });

});