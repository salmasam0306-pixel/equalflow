from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional, Dict, Any
import uvicorn
import logging
from datetime import datetime
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))

from config import Config
from services.matcher import TaskMatcher
from services.workload import WorkloadAnalyzer
from services.health import ProjectHealthAnalyzer

# Setup logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

# Initialize FastAPI
app = FastAPI(
    title="EqualFlow AI Service",
    description="AI-powered task matching and workload analysis",
    version="1.0.0"
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=Config.ALLOWED_ORIGINS if Config.ALLOWED_ORIGINS != ['*'] else ["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Initialize services
task_matcher = TaskMatcher()
workload_analyzer = WorkloadAnalyzer()
health_analyzer = ProjectHealthAnalyzer()

# ============================================
# Pydantic Models
# ============================================

class UserSkill(BaseModel):
    user_id: int
    name: str
    skills: List[str] = []
    job_scope: Optional[str] = None
    experience_level: Optional[str] = None
    completed_tasks: int = 0
    total_tasks: int = 0
    is_on_leave: bool = False
    is_outstation: bool = False

class TaskRequirement(BaseModel):
    task_id: int
    title: str
    description: Optional[str] = None
    skills_required: Optional[List[str]] = None
    experience_level: Optional[str] = None
    estimated_hours: Optional[int] = None

class MatchRequest(BaseModel):
    task: TaskRequirement
    candidates: List[UserSkill]

class WorkloadRequest(BaseModel):
    user_id: int
    active_tasks: int
    total_hours: Optional[float] = None

class ProjectHealthRequest(BaseModel):
    project_id: int
    total_tasks: int
    completed_tasks: int
    overdue_tasks: int
    blocked_tasks: int

# ============================================
# API Endpoints
# ============================================

@app.get("/")
async def root():
    return {
        "service": "EqualFlow AI Service",
        "status": "running",
        "version": "1.0.0",
        "timestamp": datetime.now().isoformat()
    }

@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "timestamp": datetime.now().isoformat()
    }

@app.post("/api/match")
async def match_task(request: MatchRequest):
    """Match a task with candidates using AI"""
    try:
        logger.info(f"Matching task {request.task.task_id} with {len(request.candidates)} candidates")
        results = task_matcher.match_task(
            task=request.task,
            candidates=request.candidates
        )
        return {
            "success": True,
            "data": results,
            "timestamp": datetime.now().isoformat()
        }
    except Exception as e:
        logger.error(f"Match error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/workload/analyze")
async def analyze_workload(users: List[WorkloadRequest]):
    """Analyze workload for users"""
    try:
        logger.info(f"Analyzing workload for {len(users)} users")
        results = workload_analyzer.analyze(users)
        return {
            "success": True,
            "data": results,
            "timestamp": datetime.now().isoformat()
        }
    except Exception as e:
        logger.error(f"Workload analysis error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/project/health")
async def analyze_project_health(request: ProjectHealthRequest):
    """Analyze project health"""
    try:
        logger.info(f"Analyzing health for project {request.project_id}")
        results = health_analyzer.analyze(
            total_tasks=request.total_tasks,
            completed_tasks=request.completed_tasks,
            overdue_tasks=request.overdue_tasks,
            blocked_tasks=request.blocked_tasks
        )
        return {
            "success": True,
            "data": results,
            "timestamp": datetime.now().isoformat()
        }
    except Exception as e:
        logger.error(f"Health analysis error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/team/rebalance")
async def rebalance_team(users: List[WorkloadRequest]):
    """Suggest task reassignment to balance workload"""
    try:
        logger.info(f"Rebalancing team with {len(users)} users")
        suggestions = workload_analyzer.suggest_rebalance(users)
        return {
            "success": True,
            "data": suggestions,
            "timestamp": datetime.now().isoformat()
        }
    except Exception as e:
        logger.error(f"Rebalance error: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    logger.info(f"Starting EqualFlow AI Service on {Config.API_HOST}:{Config.API_PORT}")
    uvicorn.run(
        "app:app",
        host=Config.API_HOST,
        port=Config.API_PORT,
        reload=Config.DEBUG
    )