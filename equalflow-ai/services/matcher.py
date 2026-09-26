from typing import List, Dict, Any
import logging
from config import Config

logger = logging.getLogger(__name__)

class TaskMatcher:
    def __init__(self):
        self.weights = Config.WEIGHTS
        self.experience_levels = Config.EXPERIENCE_LEVELS
        self.job_scope_skills = Config.JOB_SCOPE_SKILLS
    
    def match_task(self, task, candidates: List) -> List[Dict]:
        """Match a task with candidates using AI scoring"""
        results = []
        
        for candidate in candidates:
            try:
                score = self._calculate_score(task, candidate)
                match_level = self._get_match_level(score['total'])
                
                results.append({
                    'user_id': candidate.user_id,
                    'user_name': candidate.name,
                    'score': score['total'],
                    'level': match_level,
                    'breakdown': score['breakdown'],
                    'skills_match': self._get_matching_skills(task, candidate),
                    'skills_missing': self._get_missing_skills(task, candidate),
                    'recommendation': self._get_recommendation(score['total'], candidate.name)
                })
            except Exception as e:
                logger.warning(f"Error matching candidate {candidate.user_id}: {e}")
                continue
        
        results.sort(key=lambda x: x['score'], reverse=True)
        return results
    
    def _calculate_score(self, task, candidate) -> Dict:
        """Calculate weighted score for a candidate"""
        scores = {
            'skill_match': self._calculate_skill_match(task, candidate),
            'experience_match': self._calculate_experience_match(task, candidate),
            'workload_score': self._calculate_workload_score(candidate),
            'performance_score': self._calculate_performance_score(candidate),
            'availability_score': self._calculate_availability_score(candidate)
        }
        
        total = sum(scores[key] * self.weights[key] for key in scores.keys())
        
        return {
            'total': round(total, 1),
            'breakdown': {k: round(v, 1) for k, v in scores.items()}
        }
    
    def _calculate_skill_match(self, task, candidate) -> float:
        """Calculate skill match percentage using Jaccard similarity"""
        task_skills = task.skills_required or []
        candidate_skills = self._get_candidate_skills(candidate)
        
        if not task_skills:
            return 70.0
        
        if not candidate_skills:
            return 0.0
        
        # Jaccard similarity
        intersection = len(set(task_skills) & set(candidate_skills))
        union = len(set(task_skills) | set(candidate_skills))
        
        if union == 0:
            return 0.0
        
        return (intersection / union) * 100
    
    def _get_candidate_skills(self, candidate) -> List[str]:
        """Get all skills from candidate"""
        skills = list(candidate.skills or [])
        
        if candidate.job_scope:
            job_skills = self.job_scope_skills.get(candidate.job_scope, [])
            skills.extend(job_skills)
        
        return list(set(skills))
    
    def _calculate_experience_match(self, task, candidate) -> float:
        """Calculate experience match"""
        required = task.experience_level
        if not required:
            return 75.0
        
        # Estimate experience from completed tasks
        candidate_exp = min((candidate.completed_tasks / 5) + 1, 10)
        required_years = self.experience_levels.get(required, 2)
        
        if candidate_exp >= required_years:
            return 100.0
        elif candidate_exp >= required_years - 1:
            return 80.0
        elif candidate_exp >= required_years - 2:
            return 60.0
        else:
            return max(20, 60 - ((required_years - candidate_exp) * 20))
    
    def _calculate_workload_score(self, candidate) -> float:
        """Calculate workload score (lower workload = higher score)"""
        active_tasks = candidate.total_tasks - candidate.completed_tasks
        hours = active_tasks * 8
        workload = min((hours / 40) * 100, 100)
        
        if workload <= 20:
            return 100.0
        elif workload <= 40:
            return 80.0
        elif workload <= 60:
            return 60.0
        elif workload <= 80:
            return 40.0
        else:
            return 20.0
    
    def _calculate_performance_score(self, candidate) -> float:
        """Calculate performance score based on completed tasks"""
        total = candidate.total_tasks
        completed = candidate.completed_tasks
        
        if total == 0:
            return 50.0
        
        return min((completed / total) * 100, 100)
    
    def _calculate_availability_score(self, candidate) -> float:
        """Calculate availability score"""
        if getattr(candidate, 'is_on_leave', False):
            return 10.0
        if getattr(candidate, 'is_outstation', False):
            return 50.0
        return 100.0
    
    def _get_matching_skills(self, task, candidate) -> List[str]:
        """Get matching skills"""
        task_skills = task.skills_required or []
        candidate_skills = self._get_candidate_skills(candidate)
        return list(set(task_skills) & set(candidate_skills))
    
    def _get_missing_skills(self, task, candidate) -> List[str]:
        """Get missing skills"""
        task_skills = task.skills_required or []
        candidate_skills = self._get_candidate_skills(candidate)
        return list(set(task_skills) - set(candidate_skills))
    
    def _get_match_level(self, score: float) -> str:
        """Get match level based on score"""
        if score >= 85:
            return 'excellent'
        elif score >= 70:
            return 'good'
        elif score >= 50:
            return 'fair'
        else:
            return 'poor'
    
    def _get_recommendation(self, score: float, name: str) -> str:
        """Get recommendation text"""
        level = self._get_match_level(score)
        
        recommendations = {
            'excellent': f"{name} is an excellent match with strong relevant skills and experience.",
            'good': f"{name} is a good match with relevant skills but may need some guidance.",
            'fair': f"{name} has some relevant skills but may need additional training or support.",
            'poor': f"{name} may not be the best fit. Consider providing training or assigning a mentor."
        }
        
        return recommendations.get(level, f"Consider {name} for this task.")