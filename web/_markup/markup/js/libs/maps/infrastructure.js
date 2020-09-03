(function () {
    var MetalMaps = {};
    window.MetalMaps = MetalMaps;

    MetalMaps.layouts = {};
    MetalMaps.layoutsCfgs = {};
    MetalMaps.layoutFactory = function(layoutName)
    {
        if (typeof MetalMaps.layouts[layoutName] != 'undefined') {
            return MetalMaps.layouts[layoutName];
        }

        var layotCfg = MetalMaps.layoutsCfgs[layoutName];

        return MetalMaps.layouts[layoutName] = ymaps.templateLayoutFactory.createClass($(layotCfg.template).html(), layotCfg.methods);
    };

    MetalMaps.icons = {};
    MetalMaps.iconsCfgs = {};
    MetalMaps.iconFactory = function(iconName)
    {
        if (typeof MetalMaps.icons[iconName] != 'undefined') {
            return MetalMaps.icons[iconName];
        }

        var iconCfg = MetalMaps.iconsCfgs[iconName];

        var icon = {
            iconLayout: 'default#image',
            iconImageHref: iconCfg.icon,
            iconImageSize: iconCfg.size,
            iconImageOffset: iconCfg.offset
        };

        return MetalMaps.icons[iconName] = icon;
    };
    window.MetalMaps = MetalMaps;
})();
