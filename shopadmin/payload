[
	{
		"delete": {
			"objfilter": "SELECT $o:shopRoot.attrset('delete')"
		}
	},
	{
		"purge": {}
	},
	{
		"modify": {
			"data": {
				"shopRoot": {
					"INV": {
						"supplier": {
							"name": "Sony Group Corporation",
							"id": "sony"
						},
						"product": [
							{
								"name": "FinePix Pro2 3D Camera",
								"code": "3DcAM01",
								"image": "camera.jpg",
								"n:price": 300.0,
								"i:totalordered": 0,
								"manufacturer": {
									"#ref": "SELECT $o:.supplier.oid() WHERE $s:.supplier.id='sony'"
								}
							},
							{
								"name": "EXP Portable Hard Drive",
								"code": "USB02",
								"image": "external-hard-drive.jpg",
								"n:price": 800.0,
								"i:totalordered": 0, 
								"manufacturer": {
									"#ref": "SELECT $o:.supplier.oid() WHERE $s:.supplier.id='sony'"
								}
							},
							{
								"name": "Luxury Ultra thin Wrist Watch",
								"code": "wristWear03",
								"image": "watch.jpg",
								"n:price": 100.0,
								"i:totalordered": 0,
								"manufacturer": {
									"name": "TAG Heuer",
									"id": "tag"
								}
							},
							{
								"name": "XP 1155 Intel Core Laptop",
								"code": "LPN45",
								"image": "laptop.jpg",
								"n:price": 250.0,
								"i:totalordered": 0,
								"manufacturer": {
									"name": "DELL Inc",
									"id": "dell"
								}
							}
						],
						"o:supplier": [
							{
								"#append": {}
							},
							{
								"#ref": "SELECT $o:.manufacturer.oid() WHERE $s:.manufacturer.id='tag'"
							},
							{
								"#ref": "SELECT $o:.manufacturer.oid() WHERE $s:.manufacturer.id='dell'"
							}
						]
					},	
					"customer": {
						"#set": {
							"where": "$s:customer.id='c1111'"
						},
						"s:id": "c1111",
						"s:first_name": "Larry",
						"s:last_name": "Smith",
						"o:address": {
							"street": "5 Elmwood Avenue",
							"city": "Rochester",
							"state": "NY",
							"zip": "14616"
						}
					},
					"o:order": [
						{
							"#append": {}
						},
						{
							"i:order_id": 1,
							"d:datetime": "now",
							"o:cust": {
								"#ref": "SELECT $o:.customer.oid() WHERE $s:.customer.id='c1111'",
								"email": "support@schemafreesql.com"
							},
							"o:lineitem": [
								{
									"o:item": {
										"#ref": "SELECT $o:.product.oid() WHERE $s:.product.code='3DcAM01'",
										"i:totalordered": {
											"#pass": "v + 1"
										}
									},
									"i:no": 1,
									"i:qty": 1,
									"n:price": 300.0
								},
								{
									"o:item": {
										"#ref": "SELECT $o:.product.oid() WHERE $s:.product.code='wristWear03'",
										"i:totalordered": {
											"#pass": "v + 2"
										}
									},
									"i:no": 2,
									"i:qty": 2,
									"n:price": 100.0
								}
							]
						},
						{
							"i:order_id": 2,
							"d:datetime": "now",
							"o:cust": {
								"s:id": "c2222",
								"s:first_name": "Sally",
								"s:last_name": "Swanson",
								"email": "feedback@schemafreesql.com",
								"o:address": {
									"street": "7 Broadway",
									"city": "New York",
									"state": "NY",
									"zip": "10003"
								}
							},
							"o:lineitem": [
								{
									"o:item": {
										"#ref": "SELECT $o:.product.oid() WHERE $s:.product.code='LPN45'",
										"i:totalordered": {
											"#pass": "v + 2"
										}
									},
									"i:no": 1,
									"i:qty": 2,
									"n:price": 250.0
								}
							]
						}
					],
					"o:customer": [
						{
							"#append": {}
						},
						{
							"#ref": "SELECT $o:.cust.oid() WHERE $s:.cust.id='c2222'"
						}
					]
				}
			}
		}
	},
	{
		"query": {
			"sfsql": "SELECT $s:.supplier.name as name, $s:.supplier.id as id",
			"_q": "supp"
		}
	},
	{
		"query": {
			"sfsql": "SELECT $s:.product.code as code,$s:.product.name as name,$s:.product.image as image,$n:.product.price as price,$i:.product.totalordered as totordered,$s:.product.manufacturer.name as manname",
			"_q": "prod"
		}
	}, 
	{
		"query": {
			"sfsql": "SELECT $s:.customer.id as cusid,$s:.customer.first_name as fname,$s:.customer.last_name as lname,$s:.customer.email as email,$s:.customer.address.street as street,$s:.customer.address.city as city,$s:.customer.address.state as state,$s:.customer.address.zip as zip",
			"_q": "cust"
		}
	},
	{
		"query": {
			"sfsql": "SELECT $i:.order.order_id as orderid, $s:.order.lineitem.item.code as code,$s:.order.lineitem.item.name as name,$s:.order.lineitem.item.manufacturer.name as manname, $s:.order.lineitem.item.image as image, $i:.order.lineitem.no as no, $i:.order.lineitem.qty as quantity, $n:.order.lineitem.price as price, $s:.order.cust.id as cusid,$s:.order.cust.first_name as fname, $s:.order.cust.last_name as lname,$s:.order.cust.email as email,$s:.order.cust.address.street as street, $s:.order.cust.address.city as city, $s:.order.cust.address.state as state,  $s:.order.cust.address.zip as zip",
			"_q": "order"
		}
	},
	{
		"delete": {
			"objfilter": "SELECT $o:shopRoot.attrset('delete')"
		}
	},
	{
		"purge": {}
	}
]

