$("input:radio[name=radio-navbar]").bind("click", function() {
  var value;
  value = $(this).val();
  if (value === "default") {
    return $("#navbar").addClass("navbar-default").removeClass("navbar-inverse");
  } else if (value === "inverse") {
    return $("#navbar").removeClass("navbar-default").addClass("navbar-inverse");
  }
});

$("input:radio[name=radio-sidebar]").bind("click", function() {
  var value;
  value = $(this).val();
  if (value === "default") {
    return $("#sidebar").removeClass("sidebar-inverse");
  } else if (value === "inverse") {
    return $("#sidebar").addClass("sidebar-inverse");
  }
});

$("input:radio[name=radio-color]").bind("click", function() {
  var value;
  value = $(this).val();
  if (value === "blue") {
    return $("body").removeClass("flat-green").addClass("flat-blue");
  } else if (value === "green") {
    return $("body").removeClass("flat-blue").addClass("flat-green");
  }
});
