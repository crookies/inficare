/*
 * File: app/view/DayEditView.js
 *
 * This file was generated by Sencha Architect version 2.2.2.
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

Ext.define('inficare.view.DayEditView', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.dayeditview',

    id: 'dayEditViewId',
    layout: {
        align: 'stretch',
        type: 'vbox'
    },
    bodyBorder: false,
    header: false,
    title: 'My Panel',

    initComponent: function() {
        var me = this;

        Ext.applyIf(me, {
            bodyCls: [
                'inficare-splitbg',
                'x-panel-body-default',
                'x-box-layout-ct'
            ],
            dockedItems: [
                {
                    xtype: 'toolbar',
                    flex: 1,
                    dock: 'top',
                    itemId: 'dayEditToolbarId',
                    items: [
                        {
                            xtype: 'button',
                            itemId: 'saveId',
                            glyph: 'xe00d@fcrooks',
                            text: 'Sauver'
                        },
                        {
                            xtype: 'tbspacer',
                            width: 30
                        },
                        {
                            xtype: 'button',
                            itemId: 'delVisitBtnId',
                            glyph: 'xe00e@fcrooks',
                            text: 'Effacer'
                        },
                        {
                            xtype: 'tbfill'
                        },
                        {
                            xtype: 'datefield',
                            itemId: 'visitDateFieldId',
                            fieldLabel: 'Date tournée',
                            editable: false,
                            format: 'd/m/Y'
                        }
                    ]
                }
            ],
            items: [
                {
                    xtype: 'treepanel',
                    flex: 1,
                    itemId: 'dayVisitTree',
                    resizeHandles: 's',
                    animCollapse: false,
                    header: false,
                    title: 'Day Visit List',
                    hideHeaders: false,
                    store: 'VisitEditTreeStore',
                    animate: false,
                    rootVisible: false,
                    viewConfig: {
                        itemId: 'dayTreeViewId',
                        rootVisible: false,
                        plugins: [
                            Ext.create('Ext.tree.plugin.TreeViewDragDrop', {
                                ddGroup: 'dayVisitDd',
                                dragText: 'Drag & drop pour réorganiser'
                            })
                        ]
                    },
                    columns: [
                        {
                            xtype: 'treecolumn',
                            width: 200,
                            sortable: false,
                            dataIndex: 'namev',
                            menuDisabled: true,
                            text: 'Visite'
                        },
                        {
                            xtype: 'gridcolumn',
                            renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                                var cstore = Ext.getStore("NurseStore");
                                var idx;

                                if (value<=0)
                                return "";
                                else
                                {
                                    idx = cstore.findExact('idn',value);

                                    if(idx<0)
                                    return "";
                                    else
                                    {
                                        var rec = cstore.getAt(idx);
                                        return rec.get('namen');
                                    }
                                }


                            },
                            width: 100,
                            sortable: false,
                            dataIndex: 'nurseid',
                            menuDisabled: true,
                            text: 'Soignant',
                            editor: {
                                xtype: 'combobox',
                                editable: false,
                                displayField: 'namen',
                                forceSelection: true,
                                store: 'NurseStore',
                                typeAhead: true,
                                valueField: 'idn'
                            }
                        },
                        {
                            xtype: 'gridcolumn',
                            sortable: false,
                            dataIndex: 'patientcare',
                            menuDisabled: true,
                            text: 'Soins',
                            flex: 1
                        },
                        {
                            xtype: 'gridcolumn',
                            sortable: false,
                            dataIndex: 'patientinfo',
                            menuDisabled: true,
                            text: 'Détail',
                            flex: 1
                        },
                        {
                            xtype: 'gridcolumn',
                            renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                                return value.replace(/<\/?[^>]+(>|$)/g, " ");

                            },
                            sortable: false,
                            dataIndex: 'visitinfo',
                            menuDisabled: true,
                            text: 'Visit Info',
                            flex: 2,
                            editor: {
                                xtype: 'textfield'
                            }
                        }
                    ],
                    plugins: [
                        Ext.create('Ext.grid.plugin.CellEditing', {

                        })
                    ],
                    selModel: Ext.create('Ext.selection.RowModel', {

                    })
                },
                {
                    xtype: 'splitter',
                    collapseOnDblClick: false
                },
                {
                    xtype: 'container',
                    height: 50,
                    itemId: 'visitinfocard',
                    layout: {
                        type: 'card'
                    },
                    items: [
                        {
                            xtype: 'textareafield',
                            itemId: 'visitinfodisplay',
                            fieldLabel: 'Label',
                            hideLabel: true,
                            readOnly: true
                        }
                    ]
                },
                {
                    xtype: 'splitter',
                    collapsible: true,
                    performCollapse: true
                },
                {
                    xtype: 'form',
                    height: 160,
                    itemId: 'dayCommentId',
                    layout: {
                        type: 'fit'
                    },
                    collapseDirection: 'bottom',
                    collapsible: true,
                    header: false,
                    title: 'Day Comment',
                    api: {
    load: QueryVisitsDb.getDayComment,
	submit: QueryVisitsDb.saveDayComment
},
                    paramOrder: 'visitDate',
                    trackResetOnLoad: true,
                    items: [
                        {
                            xtype: 'htmleditor',
                            height: 150,
                            overflowX: 'auto',
                            overflowY: 'auto',
                            name: 'daycomment',
                            validateOnChange: false,
                            enableColors: false,
                            enableLinks: false,
                            enableSourceEdit: false
                        },
                        {
                            xtype: 'hiddenfield',
                            fieldLabel: 'Label',
                            name: 'idv'
                        }
                    ]
                }
            ]
        });

        me.processDayEditView(me);
        me.callParent(arguments);
    },

    processDayEditView: function(config) {

    }

});