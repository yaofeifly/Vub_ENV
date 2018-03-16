require('should');
var serialize = require('..');

var obj = {
  name: 'Bob',
  say: function() {
    return 'hi ' + this.name;
  },
  nl: null
};

var objWithSubObj = {
  obj: {
    name: 'Jeff',
    say: function() {
      return 'hi ' + this.name;
    }
  }
};

var objCircular = {};
objCircular.self = objCircular;

var objWithNativeCode = {
  method: {}.hasOwnProperty
};

var objWithNativeCode = {
  obj: {
    method: {}.hasOwnProperty
  },
  method: {}.hasOwnProperty
};

describe('Serialize#serialize(obj, ignoreNativeCode)', function() {
  it('should return a string', function() {
    serialize.serialize(obj).should.be.a('string');
  });

  it('should serialize a object with circular without error', function() {
    (function() {
      serialize.serialize(objCircular);
    }).should.not.throwError();
  });

  it('should throw an error when serialize a object with native code', function() {
    (function() {
      serialize.serialize(objWithNativeCode);
    }).should.throwError(/^Can't/);
  });

  it('should not throw an error when serialize a object with native code but have ignoreNativeCode flaged', function() {
    (function() {
      serialize.serialize(objWithNativeCode, true);
    }).should.not.throwError();
  });
});

describe('Serialize#unserialize(obj)', function() {
  it('should return an object', function() {
    var objSer = serialize.unserialize(serialize.serialize(obj));
    objSer.should.be.a('object');
    (null === objSer.nl).should.be.true;
  });

  it('should be unserialize a object including a function', function() {
    serialize.unserialize(serialize.serialize(obj)).say().should.equal('hi Bob');
  });

  it('should be unserialize a sub object including a function', function() {
    serialize.unserialize(serialize.serialize(objWithSubObj)).obj.say().should.equal('hi Jeff');
  });

  it('should be able to unserialize a object with circular', function() {
    serialize.unserialize(serialize.serialize(objCircular)).self.self.should.be.a('object');
  });

  it('should throw an error when unserialize a object with native code', function() {
    (function() {
      serialize.unserialize(serialize.serialize(objWithNativeCode, true)).method();
    }).should.throwError(/^Call a native function unserialized/);
  });
});

