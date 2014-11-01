/*
 * File: app/view/EditDayRoundView.js
 *
 * This file was generated by Sencha Architect version 3.0.0.
 * http://www.sencha.com/products/architect/
 *
 * This file requires use of the Ext JS 4.2.x library, under independent license.
 * License of Sencha Architect does not include license for Ext JS 4.2.x. For more
 * details see http://www.sencha.com/license or contact license@sencha.com.
 *
 * This file will be auto-generated each and everytime you save your project.
 *
 * Do NOT hand edit this file.
 */

Ext.define('inficare.view.EditDayRoundView', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.editdayroundview',

    requires: [
        'inficare.view.DayEditView',
        'inficare.view.RoundSource'
    ],

    layout: {
        type: 'border'
    },
    collapsible: false,
    header: false,
    title: 'My Panel',

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {
            items: [
                {
                    xtype: 'dayeditview',
                    flex: 2,
                    region: 'center',
                    split: true
                },
                {
                    xtype: 'roundsource',
                    itemId: 'dayRoundSourceId',
                    flex: 1,
                    region: 'west',
                    split: true
                }
            ]
        });

        me.callParent(arguments);
    }

});