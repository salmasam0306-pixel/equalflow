import os
from dotenv import load_dotenv
from pathlib import Path

load_dotenv()

class Config:
    # API Settings
    API_HOST = os.getenv('API_HOST', '0.0.0.0')
    API_PORT = int(os.getenv('API_PORT', 8001))
    DEBUG = os.getenv('DEBUG', 'True').lower() == 'true'
    
    # CORS
    ALLOWED_ORIGINS = os.getenv('ALLOWED_ORIGINS', '*').split(',')
    
    # Model weights for matching
    WEIGHTS = {
        'skill_match': 0.35,
        'experience_match': 0.20,
        'workload_score': 0.20,
        'performance_score': 0.15,
        'availability_score': 0.10
    }
    
    # Job scope to skills mapping
    JOB_SCOPE_SKILLS = {
        'civil_engineer': ['Road Design', 'Drainage System', 'Earthworks', 'Slope Design', 'Water Reticulation'],
        'structural_engineer': ['Structural Design', 'Steel Structure', 'Reinforced Concrete', 'Foundation Design', 'Piling'],
        'drafter': ['AutoCAD', 'Civil 3D', 'Revit', 'MicroStation', 'BIM Modeling'],
        'iow_road': ['Road Design', 'Highway Engineering', 'Site Supervision', 'Quality Control'],
        'iow_drainage': ['Drainage System', 'Water Reticulation', 'Sewerage System', 'Site Supervision'],
        'iow_earthwork': ['Earthworks', 'Site Grading', 'Slope Design', 'Quality Control'],
        'iow_wall': ['Structural Design', 'Reinforced Concrete', 'Foundation Design', 'Site Supervision'],
        'iow_bridge': ['Structural Design', 'Steel Structure', 'Foundation Design', 'Site Supervision'],
        'clerk': ['Documentation', 'Submissions', 'OSC', 'BOMBA', 'JPS', 'IWK', 'Contract Administration'],
        'project_manager': ['Project Management', 'Scheduling', 'Cost Estimation', 'Contract Administration'],
        'site_supervisor': ['Site Supervision', 'Quality Control', 'Safety Management'],
        'quality_control': ['Quality Control', 'Documentation', 'Inspection']
    }
    
    # Experience level mapping
    EXPERIENCE_LEVELS = {
        'entry': 1,
        'mid': 2,
        'senior': 3,
        'expert': 4
    }