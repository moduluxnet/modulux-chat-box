jQuery(function ($) {
  let frame = null;

  $(document).on("click", ".mlx-media-pick", function (e) {
    e.preventDefault();
    const wrap = $(this).closest(".mlx-media");
    const idField = wrap.find(".mlx-media-id");
    const preview = wrap.find(".mlx-media-preview");

    if (frame) frame.open();
    frame = wp.media({
      title: "Select image",
      button: { text: "Use this image" },
      multiple: false,
    });

    frame.on("select", function () {
      const attachment = frame.state().get("selection").first().toJSON();
      idField.val(attachment.id);
      preview.html(
        `<img src="${attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url}" alt="" />`,
      );
    });

    frame.open();
  });

  $(document).on("click", ".mlx-media-clear", function (e) {
    e.preventDefault();
    const wrap = getWrap(this);
    wrap.find(".mlx-media-id").val("");
    wrap.find(".mlx-media-preview").empty();
  });

  // WP color picker
  $(".mlx-color-field").wpColorPicker();

  // Toggle contact fields
  function toggleContactFields() {
    const mode = $("#mlx_contact_mode").val();
    $(".mlx-contact-whatsapp").toggle(mode === "whatsapp");
    $(".mlx-contact-custom").toggle(mode === "custom");
  }

  function toggleLauncherFields() {
    const type = $(
      'input[name="mlx_chat_box_options[launcher_icon_type]"]:checked',
    ).val();
    $(".mlx-launcher-dashicon").toggle(type === "dashicon");
    $(".mlx-launcher-image").toggle(type === "image");
  }

  toggleContactFields();
  toggleLauncherFields();

  $(document).on("change", "#mlx_contact_mode", toggleContactFields);
  $(document).on(
    "change",
    'input[name="mlx_chat_box_options[launcher_icon_type]"]',
    toggleLauncherFields,
  );

  function togglePositionFields() {
    const mode = $("#mlx_position_mode").val();
    $(".mlx-position-custom,.mlx-position-custom-mobile").toggle(mode === "custom");
  }

  togglePositionFields();
  $(document).on("change", "#mlx_position_mode", togglePositionFields);
});
