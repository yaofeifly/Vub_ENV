# node-serialize

Serialize a object including it's function into a JSON.

[![Build Status](https://travis-ci.org/luin/serialize.png?branch=master)](https://travis-ci.org/luin/serialize)

## Install

    npm install node-serialize

## Usage

    var serialize = require('node-serialize');

Serialize an object including it's function:


    var obj = {
      name: 'Bob',
      say: function() {
        return 'hi ' + this.name;
      }
    };

    var objS = serialize.serialize(obj);
    typeof objS === 'string';
    serialize.unserialize(objS).say() === 'hi Bob';

Serialize an object with a sub object:

    var objWithSubObj = {
      obj: {
        name: 'Jeff',
        say: function() {
          return 'hi ' + this.name;
        }
      }
    };

    var objWithSubObjS = serialize.serialize(objWithSubObj);
    typeof objWithSubObjS === 'string';
    serialize.unserialize(objWithSubObjS).say() === 'hi Jeff';

Serialize a circular object:

    var objCircular = {};
    objCircular.self = objCircular;

    var objCircularS = serialize.serialize(objCircular);
    typeof objCircularS === 'string';
    typeof serialize.unserialize(objCircularS).self.self.self.self === 'object';

