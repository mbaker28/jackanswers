import { startStimulusApp } from '@symfony/stimulus-bundle';
import OracleController from './controllers/oracle_controller.js';

const app = startStimulusApp();
app.register('oracle', OracleController);
