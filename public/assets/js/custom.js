$(document).ready(function() {
 $("#show-hide-filters").on("click",function(){
      if($(".show-filters").is(":visible")){
          $("#button-filters").removeClass("ni ni-bold-up")
          $("#button-filters").addClass("ni ni-bold-down")
      }else if($(".show-filters").is(":hidden")){
          $("#button-filters").removeClass("ni ni-bold-down")
          $("#button-filters").addClass("ni ni-bold-up")
      }
      $(".show-filters").slideToggle();
  });

  let invoiceTable = new DataTable('#invoiceTable',{
    responsive: true,
    autoWidth:false
  });
});