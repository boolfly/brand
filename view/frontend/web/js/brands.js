define([
    "underscore",
    "jquery",
    "uiComponent",
    "ko",
    "Magento_Ui/js/block-loader"
], function (_, $, Component, ko, blockLoader) {
    "use strict";

    return Component.extend({
        isLoading: ko.observable(true),
        initialize: function () {
            this._super();
            this.alphaSelected = ko.observable();
            this.isVisible = ko.observable(true);
            let data = _.sortBy(this.items, function (item) {
                return item.name.toLowerCase();
            });
            this.groupData = _.groupBy(data, function (item) {
                return item.name[0] !== undefined ? item.name[0].toLowerCase(): '';
            });
            this.alpha = ko.observableArray(_.keys(this.groupData));

            this.alpha.unshift('all');
            this.item_data = ko.observableArray();
            this.goto('all');
        },

        goto: function (data) {
            this.item_data.removeAll();
            this.alphaSelected(data);
            let self = this,
                items = {};
            if (data === 'all') {
                this.isVisible(true);
                items = self.groupData;
            } else {
                this.isVisible(false);
                if (self.groupData[data] !== undefined){
                    items[data] = self.groupData[data];
                } else {
                    items = self.groupData;
                }
            }
            _.each(items, function (item, fname) {
                self.item_data.push({'text' : fname, 'items': item});
            });
            this.isLoading(false);
        }
    });
});