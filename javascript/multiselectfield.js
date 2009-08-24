(function($){
  // SilverStripe loader
  SSMultiSelectFieldLoader = function(){
    var multiSelectField = this;
    $('.add', this).click(function(){
      var source = $('select:eq(0)', multiSelectField);
      var destination = $('select:eq(1)', multiSelectField);
      var hidden = $('input:eq(0)', multiSelectField);
      var addedValues = source.val();
      addedValues.push(hidden.val());
      hidden.val(addedValues.join(','));
      return !$('option:selected', source).remove().appendTo(destination);
    });
    $('.remove', this).click(function(){
      var source = $('select:eq(0)', multiSelectField);
      var destination = $('select:eq(1)', multiSelectField);
      var hidden = $('input:eq(0)', multiSelectField);
      var newValues = new Array();
      $('option:not(:selected)', destination).each(function(){
        newValues.push(this.value);
      });
      hidden.val(newValues.join(','));
      return !$('option:selected', destination).remove().appendTo(source);
    });
    var fieldForm = $(this).parents('form:eq(0)');
    fieldForm.submit(function(){
      $('select:eq(1) option', multiSelectField).each(function(i){
        $(this).attr("selected", "selected");
      });
    });
  }
  if (typeof $(document).livequery != 'undefined') {
    $('div.multiselect').livequery(SSMultiSelectFieldLoader);
  }
  else 
    $(document).ready(function(){
      $('div.multiselect').each(SSMultiSelectFieldLoader);
    });
})(jQuery);
