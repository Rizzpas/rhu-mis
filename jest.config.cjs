module.exports = {
  testEnvironment: 'jsdom',
  setupFilesAfterEnv: ['<rootDir>/tests/js/setup.js'],
  testMatch: ['<rootDir>/tests/js/**/*.test.js'],
  transform: {
    '^.+\\.jsx?$': 'babel-jest',
  },
  transformIgnorePatterns: [
    '/node_modules/(?!(alpinejs|flowbite)/)',
  ],
  moduleFileExtensions: ['js', 'json', 'jsx', 'node'],
  collectCoverageFrom: [
    'resources/js/**/*.js',
    'public/js/**/*.js',
    '!**/node_modules/**',
    '!**/vendor/**',
  ],
  coverageReporters: ['text', 'text-summary', 'lcov', 'clover'],
};
