/*
 * @title gulpfile.babel.js
 * @description A directory file loader to include all the gulp tasks
 *
 */

// Dependencies
import gulp from 'gulp';

import { watch } from './tasks/watch';
import { build } from './tasks/build';
import { copy } from './tasks/copy';

exports.watch = watch;
exports.build = build;
exports.copyFiles = copy;
